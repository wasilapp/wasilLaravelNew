<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\Manager\ShopRevenueController;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyReview;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AssignToDelivery;

class OrderController extends Controller
{
    public function index()
    {
        $deliveryBoyId = auth()->user()->id;

        $lastOrders =  Order::with('carts', 'shop','user','userAddress','deliveryBoyReview','orderPayment','orderTime')->where('delivery_boy_id', $deliveryBoyId)->where('status' ,5)->orderBy('updated_at', 'DESC')->get();
        $currentOrders = Order::with('carts', 'shop','user','userAddress','deliveryBoyReview','orderPayment','orderTime')
        ->where('delivery_boy_id', $deliveryBoyId)->whereIn('status' ,[3,4])->orderBy('updated_at', 'DESC')->get();
        $orders_ids = auth()->user()->ordersAssignToDelivery()->pluck('order_id');

        $waitingOrders = Order::with('carts', 'shop','deliveryBoyReview','orderPayment','orderTime')->whereIn('id', $orders_ids)->where('status',1)
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


      public function acceptOrder(Request $request)
    {
        // return $request;
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
        catch(Exception $e){
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
        return Order::with('coupon', 'address','orderPayment','user','userAddress', 'shop')->find($id);
    }

    //     public function waitingOrder($id)
    // {
    //     $ordesr = auth()->user()->ordersAssignToDelivery;
    //     dd($orders);

    //     return Order::with('carts', 'coupon', 'address', 'carts.product', 'carts.product.productImages', 'shop')->find($id);

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
}
