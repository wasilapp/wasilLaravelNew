<?php

namespace App\Http\Controllers\Api\v1\Manager;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\ShopReview;
use App\Models\UserCoupon;
use App\Models\DeliveryBoy;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use App\Models\AssignToDelivery;
use App\Models\DeliveryBoyReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Manager\ShopRevenueController;
use App\Notifications\ChangeOrderStatuByShopNotification;
use App\Notifications\AssigningOrderToDeliveryByShopNotification;
use App\Notifications\Fairbase\ChangeOrderStatuByShopNotificationFcm;
use App\Notifications\Fairbase\AssigningOrderToDeliveryByShopNotificationFcm;

class OrderController extends Controller
{
    use UploadImage;
    use MessageTrait;
    use AssigningOrderToDeliveryByShopNotificationFcm;
    use ChangeOrderStatuByShopNotificationFcm;

    private $shop;
    private $order;
    private $delivery;
    public function __construct(Order $order, Shop $shop,DeliveryBoy $delivery)
    {
        $this->shop = $shop;
        $this->order = $order;
        $this->delivery = $delivery;
    }

    //TODO : validation in authentication order
    public function index(Request $request)
    {


        $shop = auth()->user()->shop;
        if($shop) {
            $orders = Order::with('statu','carts.subCategory', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('shop_id', '=', $shop->id)
                ->orderBy('updated_at', 'DESC')->get();
            return $orders;
        }
        return response(['errors' => ['You have not any shop yet']], 504);

    }
    public function getByStatus($status,$type,Request $request)
    {
        $shop_id = auth()->user()->shop->id;
        $status = strtolower($status);
        $type = strtolower($type);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        switch($status) {
            case('accepted_by_shop'):
                $orders = $this->order
                        ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                        ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                        ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                        ->where('shop_id', $shop_id)
                        ->orderBy('updated_at', 'DESC')
                        ->where('status', Order::$ORDER_ACCEPTED_SHOP)
                        ->where('order_type', $type)
                        ->select('orders.*',
                            'managers.email as shop_email',
                            'managers.password as shop_password',
                            'managers.mobile as shop_mobile',
                            'managers.address as shop_address',
                            'managers.avatar_url as shop_avatar_url',
                            'managers.license as shop_license',
                            'managers.is_approval as shop_is_approval'
                            )
                        ->get();
            break;

            case('rejected_by_shop'):
                $orders = $this->order
                    ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                    ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                    ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                    ->where('shop_id', $shop_id)
                    ->orderBy('updated_at', 'DESC')
                    ->where('status', Order::$ORDER_REJECTED_BY_SHOP)
                    ->where('order_type', $type)
                    ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                    ->get();
            break;
            case('cancelled_by_shop'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_SHOP)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('assign_shop_to_delivery'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;
            // driver
            case('accepted_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ACCEPTED_BY_DELIVERY)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('rejected_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REJECTED_BY_DELIVERY)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('cancelled_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_DELIVERY)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            // user
            case('pending'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_WAIT_FOR_CONFIRMATION)
                ->where('order_type', $type)
                ->select('orders.*',
                    'managers.email as shop_email',
                    'managers.password as shop_password',
                    'managers.mobile as shop_mobile',
                    'managers.address as shop_address',
                    'managers.avatar_url as shop_avatar_url',
                    'managers.license as shop_license',
                    'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('cancelled_by_user'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_USER)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('on_the_way'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ON_THE_WAY)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('delivered'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_DELIVERED)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            case('reviewed'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REVIEWED)
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
            break;

            default:
            $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->leftJoin('shops', 'orders.shop_id', '=', 'shops.id')
                ->leftJoin('managers', 'shops.manager_id', '=', 'managers.id')
                ->where('shop_id', $shop_id)
                ->orderBy('updated_at', 'DESC')
                ->where('order_type', $type)
                ->select('orders.*',
                        'managers.email as shop_email',
                        'managers.password as shop_password',
                        'managers.mobile as shop_mobile',
                        'managers.address as shop_address',
                        'managers.avatar_url as shop_avatar_url',
                        'managers.license as shop_license',
                        'managers.is_approval as shop_is_approval'
                    )
                ->get();
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
    public function create()
    {

    }


    public function store(Request $request)
    {

    }

    public function show($id)
    {

        return Order::with('statu','carts','carts.subCategory', 'coupon', 'address', 'shop','user', 'orderPayment','deliveryBoy')
            ->find($id);

    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {

        $order = Order::find($id);

        if(isset($request->status)) {
            if (Order::isCancelStatus($request->status)) {
                if (Order::isCancellable($order->status)) {
                    $order->status = $request->status;
                    if ($order->save()) {
                        FCMController::sendMessage("Order cancelled","Your order is cancelled by shop",User::find($order->user_id)->fcm_token);
                        TransactionController::addTransaction($id);
                        return response(['message' => ['Order status changed']], 200);
                    } else {
                        return response(['errors' => ['Order status is not changed']], 403);
                    }

                } else {
                    return response(['errors' => ['Order is already accepted. you can\'t cancel']], 403);
                }
            }
        }

        $order->status = $request->status;

        if ($order->save()) {
            $fcm_token = User::find($order->user_id)->fcm_token;
            if ($request->get('status') == 2) {
                FCMController::sendMessage("Changed Order Status", "Your order accepted by seller", $fcm_token);
            }else if($request->get('status') == 3 && Order::isOrderTypePickup($order->order_type)){
                FCMController::sendMessage("Changed Order Status", "Your order is ready. please pickup from shop", $fcm_token);
            }
            return response(['message' => ['Status updated']]);
        }
        else
            return response(['errors' => ['Something wrong']], 403);
    }


    public function destroy($id)
    {

    }

    public function showReviews($id)
    {
        $user_id = auth()->user()->id;
        $order =  Order::with('statu','carts', 'coupon', 'address', 'carts.product', 'carts.product.productImages', 'shop', 'orderPayment','deliveryBoy','carts.productItem','carts.productItem.productItemFeatures')
            ->find($id);

        $productReviews = ProductReview::where('order_id','=',$order->id)->get();
        $shopReview = ShopReview::where('user_id','=',$user_id)->first();
        $deliveryBoyReview = DeliveryBoyReview::where('order_id','=',$order->id)->first();

        $order['product_reviews'] = $productReviews;
        $order['shop_review'] = $shopReview;
        $order['delivery_boy_review'] = $deliveryBoyReview;


        return $order;

    }

    public function cancelOrRejectedOrAcceptedOrder(Request $request,$id,$status){
        try{
            $manager = auth()->user();
            $status = strtolower($status);
            DB::beginTransaction();
            $order = $this->order->find($id);
            switch($status) {
                case('accepted'):
                    if ($order->status == Order::$ORDER_WAIT_FOR_CONFIRMATION) {
                        $order->status = Order::$ORDER_ACCEPTED_SHOP;
                        $order->save();
                        $user = User::where('id',$order->user_id)->get();
                       // $admins= Admin::all();
                        Notification::send($user,new ChangeOrderStatuByShopNotification($order));
                        $this->sendChangeOrderStatuByShopNotificationFcm($order);
                        DB::commit();
                        return $this->returnMessage(trans('message.order_accept_success'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('rejected'):
                    if ($order->status == Order::$ORDER_WAIT_FOR_CONFIRMATION) {
                        $order->status = Order::$ORDER_REJECTED_BY_SHOP;
                        $order->save();
                        DB::commit();
                        return $this->returnMessage(trans('message.order_reject_success'),204);
                    } else {
                        DB::rollBack();
                        return $this->errorResponse(trans('message.something_worng'), 400);
                    }
                break;
                case('cancel'):
                    if ($order->status == Order::$ORDER_ACCEPTED_SHOP || $order->status == Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY) {
                        $order->status = Order::$ORDER_CANCELLED_BY_SHOP;
                        $order->save();
                        DB::commit();
                        return $this->returnMessage(trans('message.order_cancel_success'),204);
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

    public function orderAssignShopToDelivery($id,$delivery_id){
        try{
            $manager = auth()->user();

            DB::beginTransaction();
            $order = $this->order->find($id);

            if (!$order) {
                return $this->errorResponse(trans('message.order-not-found'),400);
            }
            $delivery = $this->delivery->find($delivery_id);

            if (!$delivery) {
                return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
            }
            if ($delivery->is_offline == 0){
                if ($order->shop->category_id == 1) {
                $carts = $order->carts;
                $deliverySubCategories = $delivery->subCategory;
                $cartspluck = $carts->pluck('sub_categories_id');
                $deliverySubCategoriespluck = $deliverySubCategories->pluck('details')->pluck('sub_category_id');
                $different =  $cartspluck->diff($deliverySubCategoriespluck);
                if (!$different->isEmpty()) {
                    return $this->errorResponse(trans('message.The driver does not provide all the required services'), 400);
                }
                foreach($carts as $item){
                    foreach($deliverySubCategories as $sub){
                        if($item->sub_categories_id == $sub->details->sub_category_id ){
                            if($item->quantity > $sub->details->available_quantity){
                                return $this->errorResponse(trans('message.This driver does not have a sufficient number of required items'), 400);
                            }
                        }
                    }
                }
                    if ($order->status == Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY){
                        return $this->errorResponse(trans('message.sorry_previously_assigned_order_driver_awaiting_approval'), 400);
                    } else {
                        if ($order->status == Order::$ORDER_ACCEPTED_SHOP) {
                            $order->status = Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY;
                            $order->save();
                            AssignToDelivery::create([
                                'delivery_boy_id' => $delivery->id,
                                'order_id'=>$order->id
                                ]);
                           //return  auth()->user()->shop;
                            Notification::send($delivery,new AssigningOrderToDeliveryByShopNotification($order));
                            $this->sendAssigOrderToDeliveryByShopNotiFcm($order, $delivery);


                          //  FCMController::sendMessage('New order available',$order, $delivery->fcm_token);

                            DB::commit();
                            return $this->returnMessage(trans('message.order_assigned_success'),204);
                        } else {
                            DB::rollBack();
                            return $this->errorResponse(trans('message.something_worng'), 400);
                        }
                    }
                } else {
                    return $this->errorResponse(trans('message.something_worng'), 400);
                }
            }else{
                return $this->errorResponse(trans('message.sorry_assign_order_driver_not_available'), 400);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
