<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Manager;
use App\Helpers\AppSetting;
use App\Models\DeliveryBoy;
use Illuminate\Http\Request;
use App\Helpers\DateTimeUtil;
use App\Models\WalletCoupons;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use App\Models\AssignToDelivery;
use App\Models\DeliveryBoyReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\Manager\ShopRevenueController;

class OrderController extends Controller
{
    use UploadImage;
    use MessageTrait;

    private $shop;
    private $order;
    private $deliveryBoy;
    private $walletCoupons;
    private $wallet;
    public function __construct(DeliveryBoy $deliveryBoy,Order $order, Shop $shop,WalletCoupons $walletCoupons,Wallet $wallet)
    {
        $this->shop = $shop;
        $this->order = $order;
        $this->deliveryBoy = $deliveryBoy;
        $this->walletCoupons = $walletCoupons;
        $this->wallet = $wallet;
    }


    public function index()
    {
        $deliveryBoyId = auth()->user()->id;

        $lastOrders =  Order::with('statu','carts.subCategory', 'shop','user','userAddress','deliveryBoyReview','orderPayment','orderTime')->where('delivery_boy_id', $deliveryBoyId)->where('status' ,5)->orderBy('updated_at', 'DESC')->get();
        $currentOrders = Order::with('statu','carts.subCategory', 'shop','user','userAddress','deliveryBoyReview','orderPayment','orderTime')
        ->where('delivery_boy_id', $deliveryBoyId)->whereIn('status' ,[3,4])->orderBy('updated_at', 'DESC')->get();
        $orders_ids = auth()->user()->ordersAssignToDelivery()->pluck('order_id');

        $waitingOrders = Order::with('statu','carts.subCategory', 'shop','deliveryBoyReview','orderPayment','orderTime')->whereIn('id', $orders_ids)->where('status',1)
            ->orderBy('updated_at', 'DESC')->get();

        $sum_lastOrders = $lastOrders->sum('shop_revenue');
        $admin_lastOrders = $lastOrders->sum('admin_revenue');
        $del_lastOrders = $lastOrders->sum('delivery_fee');

        $sum_currentOrders = $currentOrders->sum('shop_revenue');
        $admin_currentOrders =  $currentOrders->sum('admin_revenue');
        $del_currentOrders =  $currentOrders->sum('delivery_fee');

        $sum_waitingOrders = $waitingOrders->sum('shop_revenue');
        $admin_waitingOrders = $waitingOrders->sum('admin_revenue');
        $del_waitingOrders = $waitingOrders->sum('delivery_fee');

            $orders =['last_orders' => $lastOrders,
            'current_orders' =>$currentOrders,
            'pending_orders' => $waitingOrders,
            'admin_rev' => $admin_lastOrders + $admin_currentOrders+ $admin_waitingOrders,
            'shop_rev'=> $sum_lastOrders + $sum_currentOrders+$sum_waitingOrders,
            'delivery_rev' => $del_lastOrders+$del_currentOrders+$del_waitingOrders,
            ];
            // dd($sum_lastOrders);
            return $orders;
    }

    public function getByStatus(Request $request,$status,$type)
    {
        $delivery_boy_id = auth()->user()->id;
        $status = strtolower($status);
        $type = strtolower($type);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $deliveryBoy = $this->deliveryBoy->find($delivery_boy_id);

        switch($status) {
            case('accepted_by_shop'):
                $orders = $this->order
                        ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                        ->where('delivery_boy_id', $delivery_boy_id)
                        ->orderBy('updated_at', 'DESC')
                        ->where('status', Order::$ORDER_ACCEPTED_SHOP)
                        ->where('order_type', $type)->get();
            break;

            case('rejected_by_shop'):
                $orders = $this->order
                    ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                    ->where('delivery_boy_id', $delivery_boy_id)
                    ->orderBy('updated_at', 'DESC')
                    ->where('status', Order::$ORDER_REJECTED_BY_SHOP)
                    ->where('order_type', $type)->get();
            break;

            case('cancelled_by_shop'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_SHOP)
                ->where('order_type', $type)->get();
            break;

            case('assign_shop_to_delivery'):
              $orders_ids = $deliveryBoy->ordersAssignToDelivery()->pluck('order_id');
              $orders = $this->order->with('carts.subCategory','statu', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                    ->whereIn('id', $orders_ids)
                    ->where('status', Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY)
                    ->orderBy('updated_at', 'DESC')
                    ->where('order_type', $type)->get();

            break;
            // driver
            case('accepted_by_driver'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ACCEPTED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            case('rejected_by_driver'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REJECTED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            case('cancelled_by_driver'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            // user
            case('pending'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_WAIT_FOR_CONFIRMATION)
                ->where('order_type', $type)->get();
            break;

            case('cancelled_by_user'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_USER)
                ->where('order_type', $type)->get();
            break;

            case('on_the_way'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ON_THE_WAY)
                ->where('order_type', $type)->get();
            break;

            case('delivered'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_DELIVERED)
                ->where('order_type', $type)->get();
            break;

            case('reviewed'):
                $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REVIEWED)
                ->where('order_type', $type)->get();
            break;

            default:
            $orders = $this->order
                ->with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('delivery_boy_id', $delivery_boy_id)
                ->orderBy('updated_at', 'DESC')
                ->where('order_type', $type)->get();
        }


        if($type == 'scheduled' || $type == 'urgent'){
            foreach ($orders as $order) {
              $order->order_date_time = Carbon::parse($order->orderTime->order_date . ' ' . $order->orderTime->order_time_from);
            }
            $result = $orders->sortBy('order_date_time')->values();
        } else {
            foreach ($orders as $order) {
                $order->distance = $this->order->haversine($latitude, $longitude, $order->latitude, $order->longitude);
            }
            $result = $orders->sortBy('distance')->values();
        }


        if ($result->isNotEmpty())  {
            return $this->returnData('data', ['orders'=>$result]);
        } else {
            return $this->returnDataMessage('data', ['orders'=>$result], trans('message.any-order-yet'));
        }
    }

      public function acceptOrder(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try{
            $order = Order::where('id',$request->order_id)->first();
            // confirmation $ORDER_ON_THE_WAY = 4 , $ORDER_CANCELLED_BY_SHOP = -2
            $order->status = $request->status;
                if($request->status == 4) {
                    $order->delivery_boy_id = auth()->id();
                    if(auth()->user()->shop){
                    $order->shop_id = auth()->user()->shop->id;}
                }
                elseif($request->status == -2){
                    $fcm_token = $order->user->fcm_token;
                    FCMController::sendMessage("Changed Order Status", "Your order cancelled by seller", $fcm_token);
                }
                $order->save();
                AssignToDelivery::where('order_id', $request->order_id)->delete();

                DB::commit();
                return response(['message' => ['Your request has been successfully deliver']], 200);
        }
        catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => ['Something wrong']], 422);
        }
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        try {
            $order = Order::with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')->find($id);
            if(!$order){
                return $this->errorResponse(trans('message.order-not-found'), 200);
            } 
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            $ordersAssignToDelivery = $deliveryBoy->ordersAssignToDelivery()->where('order_id',$id )->get();

            //return $ordersAssignToDelivery;
            if ($ordersAssignToDelivery->isEmpty()) {
                return response(['errors' => 'This order is not for your'], 422);
            }
            if ($order) {
                return $this->returnData('data', ['order'=>$order]);
            } 
    

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
        
    }

    //     public function waitingOrder($id)
    // {
    //     $ordesr = auth()->user()->ordersAssignToDelivery;
    //     dd($orders);

    //     return Order::with('statu','carts.subCategory', 'coupon', 'address', 'carts.product', 'carts.product.productImages', 'shop')->find($id);

    // }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        $deliveryBoyId = auth()->user()->id;
        $order = Order::with('user')->find($id);



        if (!$order) {
            return response(['errors' => 'This order is not for your'], 422);
        } else if ($order->delivery_boy_id != $deliveryBoyId) {
            return response(['errors' => 'This order is not for your'], 422);
        }
        $deliveryBoy = DeliveryBoy::find($deliveryBoyId);
        if (isset($request->latitude) && isset($request->longitude)) {
            $deliveryBoy->latitude = $request->latitude;
            $deliveryBoy->longitude = $request->longitude;
        }

        if ($order->status > 3) {
            if (isset($request->latitude) && isset($request->longitude)) {
                $order->latitude = $request->latitude;
                $order->longitude = $request->longitude;
            }
        }

        if (isset($request->status)) {
            if ($request->status == 5) {

                $this->validate($request, [
                    'otp' => 'required'
                ]);

                if($order->otp !== $request->otp){
                    return response(['errors' => ['OTP is incorrect']], 422);
                }


                if (!ShopRevenueController::storeRevenueWithDeliveryBoy($order->id)) {
                    return response(['errors' => ['Delivery is in wrong']], 422);
                }

                $deliveryBoy->is_free = true;
            }

            if ($order->status != $request->status) {
                $order->status = $request->status;
                $fcm_token = $order->user->fcm_token;
                if ($request->status == 4) {
                    FCMController::sendMessage("Order status changed", "Your order is picked up and delivery boy is on the way", $fcm_token, 'order');
                } else if ($request->status == 5) {
                    FCMController::sendMessage("Order status changed", "Your order is delivered. Please review our product", $fcm_token, 'order');
                    $shopManager = Manager::find(Shop::find($order->shop_id)->manager_id);
                    if($shopManager)
                        FCMController::sendMessage("Order Delivered","Order delivered", $shopManager->fcm_token);

                }
            }
        }


        if ($order->save() && $deliveryBoy->save()) {
            return response(['message' => ['Your request has been successfully deliver']], 200);

        } else {
            return response(['errors' => ['Something wrong']], 422);
        }

    }


    public function destroy($id)
    {

    }

    public function showReview($id)
    {
        return DeliveryBoyReview::with('user')->where('order_id', '=', $id)->first();
    }

    public function manageStatusOrder(Request $request,$id,$status){
        try{
           // dd($request->all());
            $delivery = auth()->user();
            $status = strtolower($status);
            DB::beginTransaction(); 
            $order = $this->order->find($id);
            if (!$order) {
                return response(['errors' => 'This order is not found'], 422);
            } 
            $deliveryBoy = $this->deliveryBoy->find($delivery->id);

            $ordersAssignToDelivery = $deliveryBoy->ordersAssignToDelivery()->where('order_id',$id )->get();
            //return $ordersAssignToDelivery;
            if ($ordersAssignToDelivery->isEmpty()) {
                return response(['errors' => 'This order is not for your'], 422);
            }


           // return $delivery;
            switch($status) {
                case('accepted'):
                    //return 'randa';
                    if ($order->status == Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY) {

                            $carts = $order->carts;
                            $deliverySubCategories = $delivery->subCategory;

                            $cartspluck = $carts->pluck('sub_categories_id');
                            $deliverySubCategoriespluck = $deliverySubCategories->pluck('details')->pluck('sub_category_id');
                            $different =  $cartspluck->diff($deliverySubCategoriespluck);

                            $available_quantity = $deliveryBoy->available_quantity;


                            if (!$different->isEmpty()) {
                                return $this->errorResponse(trans('message.The driver does not provide all the required services'), 400);
                            }
                            foreach($carts as $item){
                                foreach($deliverySubCategories as $sub){
                                    if($item->sub_categories_id == $sub->details->sub_category_id ){
                                        if($item->quantity > $sub->details->available_quantity){
                                            return $this->errorResponse(trans('message.This driver does not have a sufficient number of required items'), 400);
                                        } else {
                                            $deliveryBoy->subCategory()->updateExistingPivot($sub, array('available_quantity' =>  $sub->details->available_quantity - $item->quantity), false);

                                            $available_quantity = $available_quantity - $item->quantity;

                                        }
                                    }
                                }
                            }
                        $order->status = Order::$ORDER_ACCEPTED_BY_DELIVERY;
                        $order->delivery_boy_id = $deliveryBoy->id;
                        $order->save();
                        /////////////////


                        $data = [
                            'available_quantity' => $available_quantity
                        ];
                        if($available_quantity === 0){
                            $data['is_offline']= 1;
                        }
                        $deliveryBoy->update($data);
                        /////////////
                        DB::commit();
                        return $this->returnMessage(trans('message.order_accept_success'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('rejected'):
                    if ($order->status == Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY) {
                        $order->status = Order::$ORDER_REJECTED_BY_DELIVERY;
                        $order->save();
                        DB::commit();
                        return $this->returnMessage(trans('message.order_reject_success'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('cancel'):
                    if ($order->status == Order::$ORDER_ACCEPTED_BY_DELIVERY || $order->status == Order::$ORDER_ON_THE_WAY) {
                        $carts = $order->carts;
                            $deliverySubCategories = $delivery->subCategory;

                            $cartspluck = $carts->pluck('sub_categories_id');
                            $deliverySubCategoriespluck = $deliverySubCategories->pluck('details')->pluck('sub_category_id');
                            $different =  $cartspluck->diff($deliverySubCategoriespluck);

                            $available_quantity = $deliveryBoy->available_quantity;


                            if (!$different->isEmpty()) {
                                return $this->errorResponse(trans('message.The driver does not provide all the required services'), 400);
                            }
                            foreach($carts as $item){
                                foreach($deliverySubCategories as $sub){
                                    if($item->sub_categories_id == $sub->details->sub_category_id ){
                                        if($item->quantity > $sub->details->available_quantity){
                                            return $this->errorResponse(trans('message.This driver does not have a sufficient number of required items'), 400);
                                        } else {
                                            $deliveryBoy->subCategory()->updateExistingPivot($sub, array('available_quantity' =>  $sub->details->available_quantity + $item->quantity), false);

                                            $available_quantity = $available_quantity + $item->quantity;

                                        }
                                    }
                                }
                            }
                        $order->status = Order::$ORDER_CANCELLED_BY_DELIVERY;
                        $order->cancellation_reason = $request->cancellation_reason;
                      //  return  $request->cancellation_reason;
                        $order->save();
                        $data = [
                            'available_quantity' => $available_quantity
                        ];
                        if($available_quantity === 0){
                            $data['is_offline']= 1;
                        }
                        $deliveryBoy->update($data);



                        DB::commit();
                        return $this->returnMessage(trans('message.order_cancel_success'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('on_the_way'):

                    $currentTime = Carbon::now()->setTimezone(AppSetting::$timezone)->format('d-m-Y h:ia');
                    $currentTime = Carbon::parse($currentTime);
                    $orderDateTimeFrom = Carbon::parse($order->orderTime->order_date . ' ' . $order->orderTime->order_time_from);
                    $orderDateTimeTo = Carbon::parse($order->orderTime->order_date . ' ' . $order->orderTime->order_time_to);
                    if ($order->status == Order::$ORDER_ACCEPTED_BY_DELIVERY) {
                        if($order->order_type == 'scheduled'){

                            if ($currentTime->between($orderDateTimeFrom, $orderDateTimeTo)) {
                                $order->status = Order::$ORDER_ON_THE_WAY;
                                $order->save();
                                DB::commit();
                                return $this->returnMessage(trans('message.order_on_the_way'),204);
                            }else{
                                DB::rollBack();
                                return $this->errorResponse(trans('message.order_cannot_delivered_Please_wait'), 400);
                            }
                        }elseif($order->order_type == 'urgent'){
                            $order->status = Order::$ORDER_ON_THE_WAY;
                            $order->save();
                            DB::commit();
                            return $this->returnMessage(trans('message.order_on_the_way'),204);
                        }else{
                            DB::rollBack();
                            return $this->errorResponse(trans('message.something_worng'), 400);
                        }
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('delivered'):

                    if ($order->status == Order::$ORDER_ON_THE_WAY) {
                        $order->status = Order::$ORDER_DELIVERED;
                        $order->save();

                    if( $order->is_wallet){

                        $wallet = $this->wallet->where('id', $order->wallet_id)->first();
                    //    return $wallet;
                        $walletCoupons = $this->walletCoupons->where('user_id', $order->user_id)->where('wallet_id',$order->wallet_id)->first();
                        if($walletCoupons){
                            $walletCoupons = $walletCoupons->update([
                                'usage' => $wallet->usage + $walletCoupons->usage
                            ]);
 
                        }else {
                            $dataWalletCoupons = [
                                'user_id' => $order->user_id,
                                'wallet_id' => $wallet->id,
                                'usage' => $wallet->usage,
                                'price' => $wallet->price,
                                'status' => 1,
                                'is_paid' => 1,
                            ];

                            $walletCoupons = $this->walletCoupons->create($dataWalletCoupons);
                        }
 
                    }
                    $fcm_token = $order->user->fcm_token;
                    FCMController::sendMessage("Changed Order Status", "Your order delivered ", $fcm_token);

                        DB::commit();
                        return $this->returnMessage(trans('message.order_delivered'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
