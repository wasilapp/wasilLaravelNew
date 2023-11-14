<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Models\Shop;
use App\Models\Coupon;
use App\Models\CouponShop;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use App\Models\CouponDeliveryBoy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;

class DeliveryBoyCouponController extends Controller
{
    use UploadImage;
    use MessageTrait;
    private $coupon;
    private $shop;
    private $shopCoupon;
    private $deliveryboy;
    private $deliveryCoupon;
    public function __construct(Coupon $coupon,Shop $shop,CouponShop $shopCoupon,CouponDeliveryBoy $deliveryCoupon,DeliveryBoy $deliveryboy)
    {
        $this->coupon = $coupon;
        $this->shop = $shop;
        $this->shopCoupon = $shopCoupon;
        $this->deliveryCoupon = $deliveryCoupon;
        $this->deliveryboy = $deliveryboy;
    }

    public function index()
    {
      

    }

    public static function isCouponContain($shopCoupons, $coupon)
    {

    }

    public function create()
    {

    }

    public function store(Request $request)
    {
       // return $request->all();
        try {
            $validator = Validator::make($request->all(),[
                'code' => 'required|unique:coupons',
                'description.en' => 'required',
                'description.ar' => 'required',
                'offer' => 'required|numeric|max:100|min:0"',
                'expired_at' => 'required|date|after:now',
                'min_order'=>'required',
                'max_discount'=>'required'
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            //$delivery = auth()->user();
            $deliveryboy = $this->deliveryboy->find(auth()->user()->id);
            
            $data = [
                'category_id' => 2,
                'code' => $request->code,
                'offer' => $request->offer,
                'min_order' => $request->min_order,
                'max_discount' => $request->max_discount,
                'expired_at' => $request->expired_at,
                'type' => 'custom',
                'is_primary' => false,
                'is_approval' => false,
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
            ];
            
            if(isset($request->for_new_user)){
                 $data['for_new_user'] = true;
            }else{ 
                 $data['for_new_user'] = false;
            }
            
            if(isset($request->for_only_one_time)){
                $data['for_only_one_time'] = true;
            }else{
                $data['for_only_one_time'] = false;
            }
            $coupon = Coupon::create($data);
            if ($coupon) {
                $deliveryCouponData = [
                    'delivery_boy_id' => $deliveryboy->id,
                    'coupon_id'=> $coupon->id
                ];
               // return $deliveryCouponData;
                $deliveryboy->coupons()->syncWithoutDetaching([ 
                    $coupon->id => $deliveryCouponData
                ]);
            }
        
            DB::commit();
            return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.coupon-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    
    public function selectCoupon(Request $request, $coupon)
    {
        
        try {
            DB::beginTransaction ();
            $coupon  = $this->coupon->find($coupon);
            if (!$coupon) {
                return $this->errorResponse(trans('message.coupon-not-found'),400);
            }
            if ($coupon->category_id == 1) {
                return $this->errorResponse(trans('message.This coupon is only for the watter category'), 403);
            }
            if ($coupon->is_approval == 0) {
                return $this->errorResponse(trans('message.coupon-not-approval'), 403);
            }
            if ($coupon->is_active == 0) {
                return $this->errorResponse(trans('message.coupon-not-active'), 403);
            }
            if ($coupon->is_primary == 0) {
                return $this->errorResponse(trans('message.coupon-not-primary'), 403);
            }
            if ($coupon->type <> "available") {
                return $this->errorResponse(trans('message.coupon-not-available'), 403);
            }
            $deliveryboy = $this->deliveryboy->find(auth()->user()->id);
            $exist = $deliveryboy->coupons()->where('coupon_id' , $coupon->id)->get();
            
            if (!$exist->isempty()) {
                return $this->errorResponse(trans('message.This coupon has already been added'),403);
            }
            if ($coupon) {
                $deliveryCouponData = [
                    'delivery_boy_id' => $deliveryboy->id,
                    'coupon_id'=> $coupon->id
                ];
                // return $deliveryCouponData;
                $deliveryboy->coupons()->syncWithoutDetaching([ 
                    $coupon->id => $deliveryCouponData
                ]);
            }

            DB::commit();
            return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.Coupon added successfully'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function removeCoupon(Request $request, $coupon)
    {
        
        try {
            DB::beginTransaction ();
            $coupon  = $this->coupon->find($coupon);
            if (!$coupon) {
                return $this->errorResponse(trans('message.coupon-not-found'),400);
            }
            $deliveryboy = $this->deliveryboy->find(auth()->user()->id);
            $exist = $deliveryboy->coupons()->where('coupon_id' , $coupon->id)->get();
            //$exist = $this->deliveryCoupon->where('delivery_id',$delivery->id)->where('coupon_id' , $coupon->id)->get();

            //return $exist;
            if ($exist->isempty()) {
                return $this->errorResponse(trans('message.This coupon does not already exist'),403);
            }

            if ($coupon->category_id == 1) {
                return $this->errorResponse(trans('message.This coupon is only for the watter category'), 403);
            }
        
            
            if ($coupon->type <> "available") {
                return $this->errorResponse(trans('message.coupon-not-available'), 403);
            }
            
            if ($coupon) {
                
                $deliveryboy->coupons()->detach($coupon);
            }

            DB::commit();
            return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.Coupon removed successfully'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function deliveryboysCoupons()
    {

        try {
            $coupons = $this->coupon->where('category_id',2)->where('is_active',1)->where('is_approval', 1)->where('is_primary', 1)->orderBy('expired_at', 'ASC')->get();
            
            if ($coupons->isNotEmpty()) {
                return $this->returnData('data', ['coupons'=>$coupons]);
            } else {
                return $this->errorResponse(trans('message.any-coupons-yet'), 200);
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function myDeliveryboyCoupons()
    {
       
        try {
            
            //$shop = auth()->user()->shop;
            $delivery = $this->deliveryboy->find(auth()->user()->id);
            if(!$delivery){
                return $this->errorResponse(trans('message.deliveryBoy-not-found'), 200);
            }

            $coupons = $delivery->coupons()->get();
            if ($coupons->isNotEmpty()) {
                return $this->returnData('data', ['coupons'=>$coupons]);
            } else {
                return $this->errorResponse(trans('message.any-coupons-yet'), 200);
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $coupon = $this->coupon->where('id',$id)->first();
            if(!$coupon){
                return $this->errorResponse(trans('message.coupon-not-found'), 200);
            }

            $delivery = $this->deliveryboy->find(auth()->user()->id);
            if(!$delivery){
                return $this->errorResponse(trans('message.deliveryBoy-not-found'), 200);
            }

            $myCoupons = $delivery->coupons()->get()->pluck('id');
            $myCouponsarray = [];
            foreach( $myCoupons as  $myCoupon){
                array_push($myCouponsarray,$myCoupon);
            }
            //  return $myCouponsarray;
            //   gettype($myCoupons);
            if( in_array( $id ,$myCouponsarray ) )
            {
                if ($coupon) {
                    return $this->returnData('data', ['coupon'=>$coupon]);
                } 
    
            } else {
                if($coupon->is_active == 0 ){
                    return $this->errorResponse(trans('message.coupon-not-active'), 200);
                }
                if($coupon->is_approval == 0){
                    return $this->errorResponse(trans('message.coupon-not-approval'), 200);
                } 
                if ($coupon) {
                    return $this->returnData('data', ['coupon'=>$coupon]);
                } 
            }

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
      
        try {
            
            $validator = Validator::make($request->all(),[
                'code' => 'required|unique:coupons,code,' . $id,
                
            ]);
            
            if(isset($request->offer)){
                $this->validate($request, [
                    'offer'=> 'numeric|max:100|min:0',
                ]);
            }
            if(isset($request->expired_at)){
                $this->validate($request, [
                    'expired_at'=> 'date|after:now',
                ]);
            }
           // return  $id;
            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            //return $id;
            $coupon = $this->coupon->where('id',$id)->first();
            
            if(!$coupon){
                return $this->errorResponse(trans('message.coupon-not-found'), 200);
            }
            if ($coupon->is_primary == 1) {
                return $this->errorResponse(trans('message.coupon-primary'), 403);
            }
            //$delivery = auth()->user();
          //  return $coupon;
            $deliveryboy = $this->deliveryboy->find(auth()->user()->id);
            $exist = $deliveryboy->coupons()->where('coupon_id' , $coupon->id)->get();
            //$exist = $this->deliveryCoupon->where('delivery_id',$delivery->id)->where('coupon_id' , $coupon->id)->get();

           // return $exist;
            if ($exist->isempty()) {
                return $this->errorResponse(trans('message.You do not have permission to modify this coupon'),403);
            }
            if (!$exist->isempty() && $coupon->type == "custom") {
            
            $data = [
                'category_id' => 2,
                'type' => 'custom',
                'is_primary' => false,
                'is_approval' => false,
            ];
            
            if(isset($request->code)){
                $data['code'] = $request->code;
            }
            if(isset($request->offer)){
                $data['offer'] = $request->offer;
            }
            if(isset($request->is_active)){
                $data['is_active'] = $request->is_active;
            }
            if(isset($request->min_order)){
                $data['min_order'] = $request->min_order;
            }
            if(isset($request->max_discount)){
                $data['max_discount'] = $request->max_discount;
            }
            if(isset($request->expired_at)){
                $data['expired_at'] = $request->expired_at;
            }
            if(isset($request->description['en'])){
                $data['description']['en'] = $request->input('description')['en'];
            }
            if(isset($request->description['ar'])){
                $data['description']['ar'] = $request->input('description')['ar'];
            }

            if(isset($request->for_new_user)){
                $data['for_new_user'] = true;
            }else{ 
                $data['for_new_user'] = false;
            }
            
            if(isset($request->for_only_one_time)){
                $data['for_only_one_time'] = true;
            }else{
                $data['for_only_one_time'] = false;
            }
           // return $data;

            
            $coupon->update($data);
            
        }
            DB::commit();
            return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.coupon-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function activation(Request $request,$id)
    {
      
        try {
            
            $validator = Validator::make($request->all(),[
                'is_active' => 'required',
            ]);
            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            //return $id;
            $coupon = $this->coupon->where('id',$id)->first();
            
            if(!$coupon){
                return $this->errorResponse(trans('message.coupon-not-found'), 200);
            }
            if ($coupon->is_primary == 1) {
                return $this->errorResponse(trans('message.coupon-primary'), 403);
            }
            
            $deliveryboy = $this->deliveryboy->find(auth()->user()->id);
            $exist = $deliveryboy->coupons()->where('coupon_id' , $coupon->id)->get();
            
            if ($exist->isempty()) {
                return $this->errorResponse(trans('message.You do not have permission to modify this coupon'),403);
            }
            if (!$exist->isempty() && $coupon->type == "custom") {
            
                $data = [
                    'is_active' => $request->is_active
                ];
                
                $coupon->update($data);
            }
            DB::commit();
            if ($request->is_active) {
                return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.The coupon has been activated successfully'));
            } else {
                return $this->returnDataMessage('data', ['coupon'=>$coupon],trans('message.The coupon has been successfully deactivated'));

            }
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function destroy($id)
    {

    }
}
