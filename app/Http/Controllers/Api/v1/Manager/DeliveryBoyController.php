<?php

namespace App\Http\Controllers\Api\v1\Manager;


use App\Helpers\DistanceUtil;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use App\Http\Trait\MessageTrait;
use App\Http\Trait\UploadImage;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyReview;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeliveryBoyController extends Controller
{
    use MessageTrait;
    use UploadImage;

    private $deliveryBoy;
    public function __construct(DeliveryBoy $deliveryBoy)
    {
        $this->deliveryBoy = $deliveryBoy;
    }

    public function index()
    {
    }


    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name.en' => 'required',
                'name.ar' => 'required',
                'mobile' => 'required|string|unique:delivery_boys',
                'email' => 'required|email|unique:delivery_boys',
                'password' => 'required|string|min:8',
                // 'category_id' => 'required',
                'car_number' => 'required',
                'driving_license' => 'required',
                'avatar_url' => 'required',
                'shop_id' => ($request->input('category_id') == 1) ? 'required' : 'nullable',
                'agency_name' => ($request->input('category_id') == 2) ? 'required' : 'nullable'
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $shop =  auth()->user()->shop;

            $data = [
                'name' => $request->get('name'),
                'car_number' => $request->get('car_number'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $shop->category_id,
                'shop_id' => $shop->id,
                'is_offline' => 1,
                'is_approval' => 1
            ];
            if ($request->input('category_id') != 2) {
                $request->merge(['agency_name' => null]);
            }
            if ($request->driving_license) {
                $path  =  $this->upload($request->driving_license,'deliveryBoys/driving_licenses');
                $data['driving_license'] = $path;
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            if (isset($request->email)) {
                $data['email'] = $request->email;
            }
            $deliveryBoy = $this->deliveryBoy->create($data);
            $accessToken = $this->deliveryBoy->createToken('authToken')->accessToken;
            DB::commit();
            $shop_type = $deliveryBoy->category->title;
            if(!$shop_type){
                $shop_type = '';
            }
            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken], trans('message.account-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        $deliveryBoyId = auth()->user()->id;
        $deliveryBoy = $this->deliveryBoy
            ->with([
                'orders' => function($query) {
                    $query->where('status', '>=', 4);
                },
                'orders.user',
                'orders.shop',
                'orders.category',
                'orders.carts',
                'orders.coupon',
                'transactions'
            ])
            ->find($deliveryBoyId);
        return $this->returnData('data', ['deliveryBoy' => $deliveryBoy]);
    }


    public function edit($id)
    {


    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction ();
            $user = auth()->user();

            $deliveryBoy = $this->deliveryBoy->findOrFail($id);
            if (!$deliveryBoy) {
                return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
            }
            $data = [
                "is_approval" => 1
            ];
            if ($request->name['en']) {
                $data['name']['en'] = $request->name['en'];
            }
            if ($request->name['ar']) {
                $data['name']['ar'] = $request->name['ar'];
            }
            if ($request->has('car_number')) {
                $data['car_number'] = $request->mobile;
            }
            if ($request->has('mobile')) {
                $data['mobile'] = $request->mobile;
            }
            if ($request->has('email')) {
                $data['email'] = $request->email;
            }
            if ($request->has('password')) {
                $password = Hash::make($request->password);
                $data['password'] = $password;
            }
            if ($request->has('latitude')) {
                $data['latitude'] = $request->latitude;
            }
            if ($request->has('longitude')) {
                $data['longitude'] = $request->longitude;
            }
            if ($request->input('category_id') != 2) {
                $request->merge(['agency_name' => null]);
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            if ($request->driving_licenses) {
                $driving_licenses  =  $this->upload($request->driving_licenses,'deliveryBoys/avatar_url');
                $data['driving_licenses'] = $driving_licenses;
            }
            $deliveryBoy->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy], trans('message.account-created-Please-wait-admin-approval'));

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction ();
            // $user = auth()->user()->id;
            $deliveryBoy = $this->deliveryBoy->findOrFail($id);
            if (!$deliveryBoy) {
                return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
            }
            $numberOfOrders = $deliveryBoy->orders()->get();
            if ($deliveryBoy->is_approval == 2) {
                if (!empty($numberOfOrders)) {
                    return $this->errorResponse(trans('message.deliveryBoy-has-orders'), 403);
                }else{
                    $deliveryBoy->ordersAssignToDelivery()->delete();
                    $deliveryBoy->transactions()->delete();
                    $deliveryBoy->delete();
                }
            }else{
                $deliveryBoy->ordersAssignToDelivery()->delete();
                $deliveryBoy->delete();
            }

            DB::commit();
            return $this->returnMessage(trans('message.deliveryBoy-deleted-successfully'),204);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }


    public function showForAssign($order_id)
    {
        $shop = auth()->user()->shop;

        if($shop) {
            $order = Order::find($order_id);

            if ($order) {
                if ($order->delivery_boy_id) {
                    return $this->errorResponse(['errors' => ['Order has already assign delivery boy']],403);
                }

                else {

                    $delivery_boys = DeliveryBoy::where('is_free', '=', true)
                        ->where('is_offline', '=', false)->get();


                    foreach ($delivery_boys as $delivery_boy) {
                        $delivery_boy['far_from_shop'] = DistanceUtil::distanceBetweenTwoLatLng($shop->latitude, $shop->longitude, $delivery_boy->latitude, $delivery_boy->longitude,);
                    }

                    return $delivery_boys;
                }
            } else {
                return $this->errorResponse(['errors' => ['Something wrong']], 403);
            }
        }
        return $this->errorResponse(['errors' => ['You have not any shop yet']], 504);
    }

    public function assign($order_id, $delivery_boy_id)
    {


        $order = Order::find($order_id);
        if ($order) {
            if ($order->delivery_boy_id) {
                 return $this->errorResponse(['errors' => ['Order has already assign delivery boy']],403);
            } else {
                $order->delivery_boy_id = $delivery_boy_id;
                $deliveryBoy = DeliveryBoy::find($delivery_boy_id);
                $deliveryBoy->is_free = false;
                $order->status = 3;
                $order->save();
                $deliveryBoy->save();
                $user = User::find($order->user_id);
                FCMController::sendMessage("Changed Order Status","Your order ready and wait for delivery boy",$user->fcm_token);
                FCMController::sendMessage('New Order','Body for notification',$deliveryBoy->fcm_token);
                return $this->returnMessage(['message' => ['Delivery boy assigned']],200);
            }
        } else {
            return $this->errorResponse(['errors' => ['Order not for yours']],403);
        }
    }

    public function getAll(){
        $shop =  auth()->user()->shop;

        if($shop) {
            $shopDeliveryBoys = DeliveryBoy::where('shop_id', '=', $shop->id)->with('subCategory')->get();
            $shopDeliveryBoysAcceptManager = DeliveryBoy::where('shop_id', '=', $shop->id)->where('is_approval', '=', 1)->with('subCategory')->get();
            $shopDeliveryBoysAcceptAdmin = DeliveryBoy::where('shop_id', '=', $shop->id)->where('is_approval', '=', 2)->with('subCategory')->get();
            $shopDeliveryBoysPending = DeliveryBoy::where('shop_id', '=', $shop->id)->where('is_approval', '=', 0)->with('subCategory')->get();
            return $this->returnData('data', [
                'allShopDeliveryBoys'=>$shopDeliveryBoys,
                'shopDeliveryBoysAcceptManager'=>$shopDeliveryBoysAcceptManager,
                'shopDeliveryBoysAcceptAdmin'=>$shopDeliveryBoysAcceptAdmin,
                'shopDeliveryBoysPending'=>$shopDeliveryBoysPending,
            ]);
        }
        return $this->errorResponse(trans('message.any-shop-yet'), 200);
    }

    public function manage($id){
        $shop = auth()->user()->shop;
        if(!$shop){
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }

        $deliveryBoy = DeliveryBoy::find($id);
        if(!$deliveryBoy->shop_id)
            $deliveryBoy->shop_id = $shop->id;
        elseif ($deliveryBoy->shop_id == $shop->id){
            $deliveryBoy->shop_id = null;
        }else{
            return $this->errorResponse(['errors' => ['You can\'t allocate this delivery boy']], 403);

        }
        if($deliveryBoy->save()){
            return $this->returnMessage(['message' => ['Allocation successful']],200);

        }else{
            return $this->errorResponse(['errors' => ['Something wrong']], 403);

        }
    }

    public function showReviews($id){

        $deliveryBoyReviews =  DeliveryBoyReview::with('user')->where('delivery_boy_id','=',$id)->get();

        return view('manager.delivery-boys.show-reviews-delivery-boy')->with([
            'deliveryBoyReviews'=>$deliveryBoyReviews
        ]);

    }


    public function accept($id){
        try {
            DB::beginTransaction ();
                $deliveryBoy =  $this->deliveryBoy::findOrFail($id);
                $deliveryBoy->is_approval = 1;
                $deliveryBoy->save();
            DB::commit();
            return $this->returnDataMessage('data', ['DeliveryBoy'=>$deliveryBoy],trans('message.deliveryBoy-accepted'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }
    public function decline($id){
        try {
            DB::beginTransaction ();
                $deliveryBoy =  $this->deliveryBoy::findOrFail($id);
                $deliveryBoy->is_approval = -1;
                $deliveryBoy->save();
            DB::commit();
            return $this->returnDataMessage('data', ['DeliveryBoy'=>$deliveryBoy],trans('message.deliveryBoy-decline'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }

}
