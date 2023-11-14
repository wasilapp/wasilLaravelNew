<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Models\Shop;
use App\Models\User;
use App\Models\DeliveryBoy;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\DeliveryBoy\LoginDeliveryBoyRequest;
use App\Http\Requests\Api\DeliveryBoy\RegisterDeliveryBoyRequest;
use App\Models\Manager;

class AuthController extends Controller
{
    use MessageTrait;
    use UploadImage;

    private $deliveryBoy;
    private $manager;
    private $user;
    public function __construct(DeliveryBoy $deliveryBoy,Manager $manager,User $user)
    {
        $this->deliveryBoy = $deliveryBoy;
        $this->manager = $manager;
        $this->user = $user;
    }
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name.en' => 'required',
                'name.ar' => 'required',
                'mobile' => 'required|string|unique:delivery_boys',
                'email' => 'required|email|unique:delivery_boys',
                'distance' => 'integer|min:10',
                'password' => 'required|string|min:8',
                'category_id' => 'required',
                'car_number' => 'required',
                'driving_license' => 'required',
                'avatar_url' => 'required',
                'shop_id' => ($request->input('category_id') == 1) ? 'required' : 'nullable',
                'agency_name' => ($request->input('category_id') == 2) ? 'required' : 'nullable',
                'referrer_link' => 'string'
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();

            $message = '';

            $uniqueId = uniqid();
            $referrer = 'driver_id=' . $uniqueId;

            $data = [
                'name' => $request->get('name'),
                'car_number' => $request->get('car_number'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $request->get('category_id'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'referrer' => $referrer,
            ];
            if ($request->has('referrer_link')) {
                $referrerLink = $request->referrer_link;
                $parts = explode('=', $referrerLink);
                $variableName = $parts[0];
                $idValue = $parts[1];
                if ($variableName === 'shop_id') {
                    $referrerShop = $this->manager->where('referrer', $request->referrer_link)->first();
                    $referrerShopId = $referrerShop->id;
                    $referrerShopName = $referrerShop->shop->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerShopId . trans('message.referral-name') . ' ' . $referrerShopName;
                    $data['referrer_link'] = $referrerLink;
                }elseif ($variableName === 'user_id') {
                    $referrerUser = $this->user->where('referrer', $request->referrer_link)->first();
                    $referrerUserId = $referrerUser->id;
                    $referrerUserName = $referrerUser->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerUserId . trans('message.referral-name') . ' ' . $referrerUserName;
                    $data['referrer_link'] = $referrerLink;
                }elseif ($variableName === 'driver_id') {
                    $referrerDeliveryBoy = $this->deliveryBoy->where('referrer', $request->referrer_link)->first();
                    $referrerDeliveryBoyId = $referrerDeliveryBoy->id;
                    $referrerDeliveryBoyName = $referrerDeliveryBoy->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerDeliveryBoyId . trans('message.referral-name') . ' ' . $referrerDeliveryBoyName;
                    $data['referrer_link'] = $referrerLink;
                }else {
                    return $this->errorResponse(trans('message.no-referral'), 400);
                }
            }
            if ($request->fcm_token) {
                $fcm_token = $request->fcm_token;
                $data['fcm_token'] = $fcm_token;
            }
            if ($request->input('category_id') != 2) {
                $request->merge(['agency_name' => null]);
            }
            if (isset($request->distance)) {
                $data['distance'] = $request->distance;
            }
            if ($request->driving_license) {
                $path  =  $this->upload($request->driving_license,'deliveryBoys/driving_licenses');
                $data['driving_license'] = $path;
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            if ($request->car_license) {
                $car_license  =  $this->upload($request->car_license,'deliveryBoys/car_license');
                $data['car_license'] = $car_license;
            }
            if (isset($request->email)) {
                $data['email'] = $request->email;
            }
            if (isset($request->total_capacity)) {
                $data['total_capacity'] = $request->total_capacity;
            }
            if (isset($request->shop_id)) {
                $data['shop_id'] = $request->shop_id;
            }
            $deliveryBoy = $this->deliveryBoy->create($data);
            $accessToken = $this->deliveryBoy->createToken('authToken')->accessToken;
            DB::commit();
            $shop_type = $deliveryBoy->category->title;
            if(!$shop_type){
                $shop_type = '';
            }
            if (!empty($message)) {
                return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken,'referrer_message' => $message], trans('message.account-created-Please-wait-admin-approval'));
            } else {
                return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken], trans('message.account-created-Please-wait-admin-approval'));
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'mobile' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $deliveryBoy = $this->deliveryBoy->where('mobile', $request->mobile)->first();

            if(!$this->deliveryBoy->where('mobile', $request->mobile)->exists()){
                return $this->errorResponse(trans('message.mobile-not-found'),403);
            }

            if(!Hash::check($request->password, $deliveryBoy->password)) {
                return $this->errorResponse(trans('message.password-correct'),400);
            }

            if($deliveryBoy->is_approval == 0){
                return $this->errorResponse(trans('admin.wait_verification_driver'),402);
            }

            if($deliveryBoy->is_approval == 1){
                return $this->errorResponse(trans('message.deliveryBoy-accepted-by-manager'),403);
            }

            if($deliveryBoy->is_approval == -1){
                return $this->errorResponse(trans('message.deliveryBoy-rejected-by-manager'),403);
            }

            if($deliveryBoy->is_approval == -2){
                return $this->errorResponse(trans('message.deliveryBoy-rejected-by-admin'),403);
            }

            $accessToken = $deliveryBoy->createToken('authToken')->accessToken;
            if(isset($request->fcm_token)){
                $deliveryBoy = $this->deliveryBoy->find($deliveryBoy->id);
                $deliveryBoy->fcm_token = $request->fcm_token;
                $deliveryBoy->save();
            }
            $shop_type = $deliveryBoy->category->title;
            if(!$shop_type){
                $shop_type = '';
            }
            DB::commit();
            return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            DB::beginTransaction ();
            $this->validate($request, [
                'is_offline' => 'required'
            ]);
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            if ($request->is_offline) {
                if ($deliveryBoy->is_free) {
                    $deliveryBoy->is_offline = $request->is_offline;
                } else {
                    return $this->errorResponse(['errors' => ['Please delivered current order then you can goes to offline']],402);
                }
            } else {
                $deliveryBoy->is_offline = $request->is_offline;
            }
            DB::commit();

            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy],trans('message.Your status has been changed'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction ();
            $deliveryBoy = auth()->user();
            $deliveryBoy = $this->deliveryBoy->where('mobile', $deliveryBoy->mobile)->first();

            if (!$deliveryBoy) {
                return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
            }
            $data = [];

            if ($request->has('name')) {
                $data['name'] = $request->name;
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
            if ($deliveryBoy->category_id == 2) {
               if ($request->has('distance')) {
                    $data['distance'] = $request->distance;
                }
            }
            if ($request->has('password')) {
                $password = Hash::make($request->password);
                $data['password'] = $password;
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            if ($request->driving_licenses) {
                $driving_licenses  =  $this->upload($request->driving_licenses,'deliveryBoys/avatar_url');
                $data['driving_licenses'] = $driving_licenses;
            }
            if ($request->car_license) {
                $car_license  =  $this->upload($request->car_license,'deliveryBoys/car_license');
                $data['car_license'] = $car_license;
            }
            if ($request->has('is_offline')) {
                $data['is_offline'] = $request->is_offline;
            }
            if ($request->has('total_capacity')) {
                $data['total_capacity'] = $request->total_capacity;
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

    public function verifyMobileNumber(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
            }

            if ($this->deliveryBoy->where('mobile', $request->input('mobile'))->exists()) {
                DB::rollBack();
                return $this->errorResponse([trans('message.mobile-already-register')], 400);
            }
            DB::commit();
            return $this->returnMessage(trans('message.verify-mobile'),204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

   public function mobileVerified(Request $request)
   {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'mobile'=>'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            $data = [
                "mobile" => $request->get('mobile'),
                "mobile_verified" => true
            ];
            $deliveryBoy->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy],trans('message.account-modified'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
   }

   public function delete(Request $request)
   {
        try {
            DB::beginTransaction();

            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            $orders = $deliveryBoy->orders;
            foreach ($orders as $order){
                if($order->status >= 4){
                    return $this->errorResponse(trans('message.You have active orders, please cancel all orders first'), 200);
                }
                $order->delete();
            }
            if($deliveryBoy->delete()){
                DB::commit();
                return $this->returnMessage(trans('message.Account-Deleted-Success'),204);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
   }
}
