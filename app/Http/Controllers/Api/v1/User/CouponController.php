<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\Shop;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\DeliveryBoy;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{
    use MessageTrait;
    private $coupon; 
    private $deliveryBoy; 

    public function __construct(DeliveryBoy $deliveryBoy, Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->deliveryBoy = $deliveryBoy;
    }
    //TODO : validation in authentication order
    public function index()
    {
        try {
            $coupons = $this->coupon->where('is_active',1)->where('is_approval', 1)->orderBy('expired_at', 'ASC')->get();
            
            if ($coupons) {
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
    public function shopsCoupons()
    {
     
        try {
            $coupons = $this->coupon->where('category_id',1)->where('is_active',1)->where('is_approval', 1)->orderBy('expired_at', 'ASC')->get();
            
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
    public function getShopCoupons($id)
    {
       
        try {
            
            $shop = Shop::where('id',$id)->first();

            if(!$shop){
                return $this->errorResponse(trans('message.shop-not-found'), 200);
            }

            $coupons = $shop->coupons()->where('is_active',1)->where('is_approval', 1)->get();
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
    public function deliveryboysCoupons()
    {
        try {
            $coupons = $this->coupon->where('category_id',2)->where('is_active',1)->where('is_approval', 1)->orderBy('expired_at', 'ASC')->get();
            
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
    public function getDeliveryboysCoupons($id)
    {
        try {
            $deliverBoy = $this->deliveryBoy->where('id',$id)->first();

            if(!$deliverBoy){
                return $this->errorResponse(trans('message.deliveryBoy-not-found'), 200);
            }

            $coupons = $deliverBoy->coupons()->where('is_active',1)->where('is_approval', 1)->get();
            
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

    public function create($id)
    {
        
    }


    public function store(Request $request)
    {


    }

    public function show($id)
    {
        try {
            $coupon = $this->coupon->where('id',$id)->first();
            if(!$coupon){
                return $this->errorResponse(trans('message.coupon-not-found'), 200);
            }
            if($coupon->is_active == 0){
                return $this->errorResponse(trans('message.coupon-not-active'), 200);
            }
            if($coupon->is_approval == 0){
                return $this->errorResponse(trans('message.coupon-not-approval'), 200);
            }

            if ($coupon) {
                return $this->returnData('data', ['coupon'=>$coupon]);
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


    public function update(Request $request)
    {

    }


    public function destroy($id)
    {

    }

}
