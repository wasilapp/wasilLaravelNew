<?php

namespace App\Http\Controllers\Api\v1\User;

use Exception;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Manager;
use App\Models\OrderTime;
use App\Models\ShopReview;
use App\Models\UserCoupon;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\WalletCoupons;
use App\Models\AssignToDelivery;
use App\Models\DeliveryBoyReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\Admin\TransactionController;

use App\Http\Controllers\Manager\ShopRevenueController;
use App\Http\Controllers\Api\v1\User\OrderPaymentController;

class OrderController extends Controller
{
    //TODO : validation in authentication order
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $orders = Order::with('carts','shop','user', 'coupon', 'address','deliveryBoy', 'orderPayment','orderTime')
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'DESC')->get();
                   return response(['orders'=>$orders]);
    }

    public function create()
    {

    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required',
            // 'carts' => 'required',
            'order' => 'required',
            //'tax' => 'required',
            'delivery_fee' => 'required',
            'total' => 'required',
            'status' => 'required',
            'order_type'=>'required',
            // 'shop_id'=>'required',
            'count' =>'required',
            'type' => 'required'
        ]);
        if($request->order_date){
            $this->validate($request, [
                'order_time'=> 'required',
                ]);
        }

        if($request->order_type==2){
            $this->validate($request,[
                'address_id' => 'required',
            ]);
        }
        
        DB::beginTransaction();

        try{
            
        if(isset($request->coupon_id)){
            $couponResponse = UserCouponController::verifyCoupon(auth()->id(),$request->coupon_id);
                 Log::info($couponResponse);
            if(!$couponResponse['success']) {
                return response(['errors' => [$couponResponse['error']]], 403);
            }
        }
        
        if($request->payment_type == 5 ){
             $wallet = WalletCoupons::where('user_id',auth()->id())->first();
            if($wallet){
            $wallet->update([
                'usage' => $wallet->usage + $request->order,
                ]);
            }
        }
        
        $orderPayment = OrderPaymentController::addPayment($request);
        
        if ($orderPayment) {
            $order = new Order();
            $order->address_id = $request->address_id;
            $order->order_payment_id = $orderPayment->id;
            $order->user_id = auth()->id();
            $order->coupon_id = $request->coupon_id;
            $order->order = $request->order;
            $order->delivery_fee = $request->delivery_fee;
            $order->total = $request->total;
            $order->status = $request->status;
            $order->order_type = $request->order_type;
            if (isset($request->coupon_discount)) {
                $order->coupon_discount = $request->coupon_discount;
            }
           
            $shop = Shop::where('id',$request->shop_id)->first();
            
            if(!$shop){
                return response(['errors' => ['This shop dose not exist']], 404);
            }
            $order->shop_id = $shop->id;
            $order->latitude = $shop->latitude;
            $order->longitude = $shop->longitude;
            $order->otp = rand(100000,999999);
            $order->count = $request->count;
            $order->type = $request->type;
            $revenue = $request->order;

            $shop_commesion = Shop::where('id' , $shop->id)->first();
           // return $shop_commesion->category()->first()->commesion;
            if($shop_commesion){
                $commesion = $shop_commesion->category()->first()->commesion;
            }
            
            $admin_revenue = $commesion;
            $shop_revenue = $revenue - $admin_revenue;
            $order->admin_revenue = $admin_revenue;
            $order->shop_revenue = $shop_revenue;
            $order->save();

            if($request->order_date){
                $orderTime = new OrderTime();
                $orderTime->order_date = $request->order_date;
                $orderTime->order_time = $request->order_time;
                $orderTime->order_id = $order->id;
                $orderTime->save();
            }
            $user_id = auth()->id();
            if(isset($request->coupon_id)) {
                $userCoupon = new UserCoupon();
                $userCoupon->user_id = $user_id;
                $userCoupon->coupon_id = $request->coupon_id;
                $userCoupon->save();
            }
        DB::commit();

             $shopManager = Manager::where('id',Shop::findorfail($order->shop_id)->manager_id)->first();
             if($shopManager)
                 FCMController::sendMessage("New Order","You have new order from ".auth()->user()->name, $shopManager->fcm_token);
             return Order::with('carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($order->id);

        } else {

            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
                DB::rollBack();
                Log::info($e->getMessage());
                return response(['errors' => ['There is something wrong']], 403);
        }
    }



    public function storeDriverOrder(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required',
            'order' => 'required',
            'delivery_fee' => 'required',
            'total' => 'required',
            'status' => 'required',
            'order_type'=>'required',
            'deliveryIds'=>'required',
            'count' => 'required',
            'type' => 'required'
        ]);

         if($request->order_date){
            $this->validate($request, [
                'order_time'=> 'required',
                ]);
        }

        $deliveryIds = explode(',', $request->deliveryIds);
        DB::beginTransaction();

        try{
        if($request->order_type==2){
            $this->validate($request,[
                'address_id' => 'required',
            ]);
        }

        $user = auth()->user();
        $user_id = $user->id;
        $request['user_id'] = $user_id;

        if(isset($request->coupon_id)){
            $couponResponse = UserCouponController::verifyCoupon($user_id,$request->coupon_id);
            if(!$couponResponse['success']) {
                return response(['errors' => [$couponResponse['error']]], 403);
            }
        }


        if($request->payment_type == 5 ){
             $wallet = WalletCoupons::where('user_id',auth()->id())->first();
            if($wallet){
            $wallet->update([
                'usage' => $wallet->usage + $request->order + $request->delivery_fee,
                ]);
            }
        }



        $orderPayment = OrderPaymentController::addPayment($request);
        if ($orderPayment) {
            $order = new Order();
            $order->address_id = $request->address_id;
            $order->order_payment_id = $orderPayment->id;
            $order->user_id = auth()->user()->id;
            $order->coupon_id = $request->coupon_id;
            $order->order = $request->order;
            $order->delivery_fee = $request->delivery_fee;
            $order->total = $request->total;
            $order->status = $request->status;
            $order->order_type = $request->order_type;
            $order->count = $request->count;
            $order->type = $request->type;
            if (isset($request->coupon_discount)) {
                $order->coupon_discount = $request->coupon_discount;
            }


            $order->latitude = auth()->user()->addresses()->first()->latitude;
            $order->longitude = auth()->user()->addresses()->first()->longitude;
            $order->otp = rand(100000,999999);

            $revenue = $request->order;

            $commesion = 0.0;
            $del_commesion = DeliveryBoy::where('id' , $deliveryIds[0])->first();
            if($del_commesion){
                $commesion = $del_commesion->category()->first()->commesion;
            }


             $admin_revenue = $commesion;
//بدها تعديل لانها رح تكون قيمة ثابتة للكل
            $shop_revenue = $revenue - $admin_revenue;
            $order->admin_revenue = $admin_revenue;
            $order->shop_revenue = $shop_revenue;



            $order->save();

            if($request->order_date){

                $orderTime = new OrderTime();
                $orderTime->order_date = $request->order_date;
                $orderTime->order_time = $request->order_time;
                $orderTime->order_id = $order->id;
                $orderTime->save();
            }
            $order = Order::find($order->id);

            if(isset($request->coupon_id)) {
                $userCoupon = new UserCoupon();
                $userCoupon->user_id = $user_id;
                $userCoupon->coupon_id = $request->coupon_id;
                $userCoupon->save();
            }

           $deliveryIds = DeliveryBoy::whereIn('id', $deliveryIds)->get();
            $data=[
                'address'=>auth()->user()->addresses()->where('id',$request->address_id),
                'total'=> $order->total,
              ];

            foreach ($deliveryIds as $delivery) {
                AssignToDelivery::create([
                    'delivery_boy_id' => $delivery->id,
                    'order_id'=>$order->id

                    ]);
                FCMController::sendMessage('New order available',$data, $delivery->fcm_token);
            }


        DB::commit();
         return Order::with('carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($order->id);


        } else {
            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
    DB::rollBack();
    return $e->getMessage();


    }}

    public function show($id)
    {

        return Order::with('carts', 'coupon', 'address', 'carts.product', 'carts.product.productImages', 'shop','user', 'orderPayment','deliveryBoy')
            ->find($id);

    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {

        $order = Order::find($id);

        $user = auth()->user();

        if(isset($request->status)) {
            if (Order::isCancelStatus($request->status)) {
                if (Order::isCancellable($order->status)) {
                    $order->status = $request->status;
                    if ($order->save()) {
                        TransactionController::addTransaction($id);
                        $shopManager = Manager::find(Shop::find($order->shop_id)->manager_id);
                        if($shopManager)
                            FCMController::sendMessage("Order cancelled","Order cancelled from ".$user->name, $shopManager->fcm_token);
                        return response(['message' => ['Order status changed']], 200);
                    } else {
                        return response(['errors' => ['Order status is not changed']], 403);
                    }

                } else {
                    return response(['errors' => ['Order is already accepted. you can\'t cancel']], 403);
                }
            }
        }


        if(isset($request->success) & isset($request->payment_id)) {

            $order = Order::with('orderPayment')->find($id);
            $order->status = 1;
            $orderPayment = OrderPayment::find($order->orderPayment->id);
            $orderPayment->success = $request->success;
            $orderPayment->payment_id = $request->payment_id;
            if ($orderPayment->save() && $order->save()) {
                $shopManager = Manager::find(Shop::find($order->shop_id)->manager_id);
                if($shopManager)
                    FCMController::sendMessage("Payment Confirmed","Order payment confirmed by".$user->name, $shopManager->fcm_token);

                return response(['message' => ['Payment Method updated']], 200);
            } else {
                return response(['errors' => ['Payment Failed please contact EMall']], 403);
            }
        }else if(isset($request->status)){
            if($request->status==5){
                $order = Order::find($id);
                if (!ShopRevenueController::storeRevenue($id)) {
                    return response(['errors' => ['Delivery is in wrong']], 422);
                }
                $order->status = $request->status;
                if($order->save()){
                    $shopManager = Manager::find(Shop::find($order->shop_id)->manager_id);
                    if($shopManager)
                        FCMController::sendMessage("Order Delivered","Order delivered from ".$user->name, $shopManager->fcm_token);

                    return response(['message' => ['Order is delivered, please rate']], 200);
                }else{
                    return response(['errors' => ['Order status is not changed']], 403);

                }
            }
        }
        return response(['errors' => ['Body is empty']], 403);
    }


    public function destroy($id)
    {

    }

    public function showReviews($id)
    {
        $user_id = auth()->user()->id;
        $order =  Order::with('carts', 'coupon', 'address', 'shop', 'orderPayment','deliveryBoy')
            ->find($id);

        // $productReviews = ProductReview::where('order_id','=',$order->id)->get();
        $shopReview = ShopReview::where('user_id','=',$user_id)->first();
        $deliveryBoyReview = DeliveryBoyReview::where('order_id','=',$order->id)->first();

        // $order['product_reviews'] = $productReviews;
        $order['shop_review'] = $shopReview;
        $order['delivery_boy_review'] = $deliveryBoyReview;


        return $order;

    }



    public function deliveryAssign(Request $request){

     $request->validate([
     'order_id'=>'required',
     'driver_id'=>'required',
     //mutasem
     ]);

     $order=Order::with('carts')->where('id',$request->order_id)->first();

        if($order){
            $order->status=2;
            $order->delivery_boy_id=$request->driver_id;
            $order->save();

            return response([
                'order'=>$order,

            ]);

        }else{
            return response(['errors' => ['the order not found']], 403);
        }








    }



}
