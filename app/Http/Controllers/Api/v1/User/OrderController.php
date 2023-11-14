<?php

namespace App\Http\Controllers\Api\v1\User;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Manager;
use App\Models\Product;
use App\Models\OrderTime;
use App\Models\ShopReview;
use App\Models\UserCoupon;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\WalletCoupons;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use App\Models\AssignToDelivery;
use App\Models\DeliveryBoyReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FCMController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Manager\ShopRevenueController;
use App\Http\Controllers\Api\v1\User\OrderPaymentController;

class OrderController extends Controller
{
    use UploadImage;
    use MessageTrait;


    private $order;
    private $delivery;
    private $coupon;
    public function __construct(Order $order, DeliveryBoy $delivery,Coupon $coupon)
    {
        $this->order = $order;
        $this->delivery = $delivery;
        $this->coupon = $coupon;
    }

    //TODO : validation in authentication order
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $orders = Order::with('statu','carts','shop.manager','user', 'coupon', 'address','deliveryBoy', 'orderPayment','orderTime')
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'DESC')->get();
           return $orders;
        if ($orders) {
            return $this->returnData('data', ['orders'=>$orders]);
        } else {
            return $this->errorResponse(trans('message.any-order-yet'), 200);
        }

    }
    public function getByStatus($status,$type,Request $request)
    {
        $user_id = auth()->user()->id;
        $status = strtolower($status);
        $type = strtolower($type);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        switch($status) {
            case('accepted_by_shop'):
                $orders = $this->order
                        ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                        ->where('user_id', $user_id)
                        ->orderBy('updated_at', 'DESC')
                        ->where('status', Order::$ORDER_ACCEPTED_SHOP)
                        ->where('order_type', $type)->get();
            break;

            case('rejected_by_shop'):
                $orders = $this->order
                    ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                    ->where('user_id', $user_id)
                    ->orderBy('updated_at', 'DESC')
                    ->where('status', Order::$ORDER_REJECTED_BY_SHOP)
                    ->where('order_type', $type)->get();
            break;

            case('cancelled_by_shop'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_SHOP)
                ->where('order_type', $type)->get();
            break;

            case('assign_shop_to_delivery'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY)
                ->where('order_type', $type)->get();
            break;
            // driver
            case('accepted_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ACCEPTED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            case('rejected_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REJECTED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            case('cancelled_by_driver'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_DELIVERY)
                ->where('order_type', $type)->get();
            break;

            // user
            case('pending'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                // ->leftJoin('products', 'orders.id', '=', 'products.order_id')
                // ->leftJoin('sub_categories', 'products.sub_categories_id', '=', 'sub_categories.id')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_WAIT_FOR_CONFIRMATION)
                ->where('order_type', $type)->get();
            break;

            case('cancelled_by_user'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_CANCELLED_BY_USER)
                ->where('order_type', $type)->get();
            break;

            case('on_the_way'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_ON_THE_WAY)
                ->where('order_type', $type)->get();
            break;

            case('delivered'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_DELIVERED)
                ->where('order_type', $type)->get();
            break;

            case('reviewed'):
                $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'DESC')
                ->where('status', Order::$ORDER_REVIEWED)
                ->where('order_type', $type)->get();
            break;

            default:
            $orders = $this->order
                ->with('statu','carts', 'shop', 'user', 'coupon', 'address', 'deliveryBoy', 'orderPayment', 'orderTime')
                ->where('user_id', $user_id)
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

    public function create()
    {

    }



    /* public function store(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required',
            'carts' => 'required',
            //'order' => 'required',
            //'tax' => 'required',
            'delivery_fee' => 'required',
            'total' => 'required',
            //'status' => 'required',
            'order_type'=>'required',
            // 'shop_id'=>'required',
            'count' =>'required',
            'type' => 'required',
            'order_type' => 'required',
            'address_id' => 'required'
        ]);
        if($request->order_date){
            $this->validate($request, [
                'order_time'=> 'required',
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
            $order->status = 1;
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
            if($request->carts){
                foreach ($request->carts as $product){
                    //return $product['sub_categories_id'];
                    Product::create([
                        'order_id'=>$order->id,
                        'sub_categories_id'=>$product['sub_categories_id'],
                        'quantity'=>$product['quantity'],
                        'price'=>$product['price'],
                        'total'=>$product['total'],
                    ]);
                }
            }
        DB::commit();

             $shopManager = Manager::where('id',Shop::findorfail($order->shop_id)->manager_id)->first();
             if($shopManager)
                 FCMController::sendMessage("New Order","You have new order from ".auth()->user()->name, $shopManager->fcm_token);
             return Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($order->id);

        } else {

            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
                DB::rollBack();
                Log::info($e->getMessage());
                return response(['errors' => ['There is something wrong']], 403);
        }
    } */


    /* public function store(Request $request)
    {
        $orders = $request->all();

           $rules = [
            '*.payment_type' => 'required',
            '*.order' => 'required',
            '*.delivery_fee' => 'required',
            '*.total' => 'required',
            '*.status' => 'required',
            '*.order_type' => 'required',
            '*.quantity' => 'required',
            '*.price' => 'required',
            '*.type' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = [];
            foreach($orders as $ord ){

                    if(isset($ord->coupon_id)){
                        $couponResponse = UserCouponController::verifyCoupon(auth()->id(),$ord->coupon_id);
                            Log::info($couponResponse);
                        if(!$couponResponse['success']) {
                            return response(['errors' => [$couponResponse['error']]], 403);
                        }
                    }

                    if($ord['payment_type'] == 5 ){
                        $wallet = WalletCoupons::where('user_id',auth()->id())->first();
                        if($wallet){
                        $wallet->update([
                            'usage' => $wallet->usage + $ord->order,
                            ]);
                        }
                    }
                    //////////////////////////////////
                    $orderPayment = new OrderPayment();
                    $orderPayment->payment_type = $ord['payment_type'] ;


                    if(isset($ord['payment_id'])){
                        $orderPayment->payment_id =$ord['payment_id'];
                    }
                    if(isset($ord['success'])){
                        $orderPayment->success = $ord['success'];
                    }

                    if(Order::isPaymentByPaystack($ord['payment_type'])){
                        $orderPayment->payment_id = OrderPaymentController::genAccessCode($ord['total']);
                    }


                    $orderPayment->save();
                    $orderPayment = $orderPayment;
                    // $orderPayment = OrderPaymentController::addPayment($ord);

                    ///////////////////////////////
                    if ($orderPayment) {
                        $order = new Order();
                        $order->address_id = $ord['address_id'];
                        $order->order_payment_id = $orderPayment->id;
                        $order->user_id = auth()->id();
                        $order->coupon_id = $ord['coupon_id'];
                        $order->order = $ord['order'];
                        $order->delivery_fee = $ord['delivery_fee'];
                        $order->total = $ord['total'];
                        $order->status = $ord['status'];
                        $order->order_type = $ord['order_type'];
                        if (isset($ord['coupon_discount'])) {
                            $order->coupon_discount = $ord['coupon_discount'];
                        }

                        $shop = Shop::where('id',$ord['shop_id'])->first();

                        if(!$shop){
                            return response(['errors' => ['This shop dose not exist']], 404);
                        }
                        $order->shop_id = $shop->id;
                        $order->latitude = $shop->latitude;
                        $order->longitude = $shop->longitude;
                        $order->otp = rand(100000,999999);
                        $order->quantity = $ord['quantity'];
                        $order->price = $ord['price'];
                        $order->type = $ord['type'];
                        $revenue = $ord['order'];

                        $shop_commesion = Shop::where('id' , $shop->id)->first();
                        if($shop_commesion){
                            $commesion = $shop_commesion->category()->first()->commesion;
                        }

                        $admin_revenue = $commesion;
                        $shop_revenue = $revenue - $admin_revenue;
                        $order->admin_revenue = $admin_revenue;
                        $order->shop_revenue = $shop_revenue;
                        $order->save();

                        if(isset($ord['order_date'])){
                            $orderTime = new OrderTime();
                            $orderTime->order_date = $ord['order_date'];
                            $orderTime->order_time = $ord['order_time'];
                            $orderTime->order_id = $order->id;
                            $orderTime->save();
                        }
                        $user_id = auth()->id();
                        if(isset($ord['coupon_id'])) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user_id = $user_id;
                            $userCoupon->coupon_id = $ord['coupon_id'];
                            $userCoupon->save();
                        }
                        // return   $ord ;
                    } else {
                        return response(['errors' => ['There is something wrong']], 403);
                    }
                    array_push($data,$order);

            }
            DB::commit();
            foreach($data as $da){
                $shopManager = Manager::where('id',Shop::findorfail($da->shop_id)->manager_id)->first();
                if($shopManager)
                    FCMController::sendMessage("New Order","You have new order from ".auth()->user()->name, $shopManager->fcm_token);
            }
            if ($data) {
                $allOrders = [];
                foreach($data as $da){
                    $or = Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($da->id);
                    array_push($allOrders,$or);
                }
                return $this->returnData('data', ['orders'=>$allOrders]);
            } else {
                return $this->errorResponse(trans('message.any-order-yet'), 200);
            }
         //   return Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($order->id);

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    } */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'payment_type' => 'required',
            'carts' => 'required',
            //'order' => 'required',
            //'tax' => 'required',
            'delivery_fee' => 'required',
            'total' => 'required',
            //'status' => 'required',
            'order_type'=>'required',
            // 'shop_id'=>'required',
            'count' =>'required',
            'type' => 'required',
            // 'order_type' => 'required',
            'address_id' => 'required'
        ]);
        if($request->order_type == 'scheduled' || $request->order_type == 'urgent'){
            $this->validate($request, [
                'order_date'=> 'required',
                'order_time_from'=> 'required',
                'order_time_to'=> 'required',
                ]);
        }
        if($request->order_type == 'urgent'){
            $this->validate($request, [
                'expedited_fees'=> 'required',
                ]);
        }
        if($request->category_id == '2'){
            $this->validate($request, [
                'delivery_boy_id'=> 'required',
                ]);
        }
        if($request->payment_type == 1){
            $this->validate($request, [
                'wallet_id'=> 'required',
                ]);
        }

        DB::beginTransaction();

        try{
            //////////////////////////////////////////////////////////
            if($request->payment_type == 1 ){
                $walletCoupons = WalletCoupons::where('user_id',auth()->id())->where('wallet_id', $request->wallet_id)->first();
                if(!$walletCoupons){
                    return $this->errorResponse(trans('message.walletCoupons-not-found'),400);
                   }
                $wallet = $walletCoupons->wallet;
                if(!$wallet){
                    return $this->errorResponse(trans('message.wallet-not-found'),400);
                }
            }
            if (isset($request->delivery_boy_id) && !$request->delivery_boy_id == null) {
                $delivery_boy_id = $request->delivery_boy_id;
                $delivery = $this->delivery->find( $delivery_boy_id);
                // return $delivery;
                if (!$delivery) {
                    return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
                }

                if ($delivery->is_offline == 0){
                    $deliverySubCategories = $delivery->subCategory;
                    if($deliverySubCategories->isEmpty()){
                        return $this->errorResponse(trans('message.deliveryBoy-does-not-have-any-item'),400);
                    }
                        $driverSubCategories = $deliverySubCategories->pluck('details')->pluck('sub_category_id');

                        $subCategoryIds = collect($request->carts)->pluck('sub_categories_id');

                        if ($subCategoryIds->diff($driverSubCategories)->isNotEmpty()) {
                            return $this->errorResponse(trans('message.The driver does not provide all the required services'), 400);
                        }

                        $carts = $request->carts;

                        foreach($carts as $item){
                            if($request->payment_type == 1 && $item['sub_categories_id'] <> $wallet->subcategory_id ){
                                return $this->errorResponse(trans('message.Sorry, the wallet does not support this item'), 400);
                            }
                            foreach($deliverySubCategories as $sub){
                                if($item['sub_categories_id'] == $sub->details->sub_category_id ){
                                    if($item['quantity'] > $sub->details->available_quantity){
                                        return $this->errorResponse(trans('message.This driver does not have a sufficient number of required items'), 400);
                                    }

                                }
                            }
                        }

                } else {
                    return $this->errorResponse(trans('message.sorry_assign_order_driver_not_available'), 400);
                }
            }

            /////////////////////////////////////////////////////////

        if(isset($request->coupon_id)){
                $couponResponse = UserCouponController::verifyCoupon(auth()->id(),$request->coupon_id);
                //return $couponResponse;
                    Log::info($couponResponse);
                if(!$couponResponse['success']) {
                    return response(['errors' => [$couponResponse['error']]], 403);
                }
        }



        $orderPayment = OrderPaymentController::addPayment($request);

        if ($orderPayment) {
            $data = [
                'address_id'=>$request->address_id,
                'order_payment_id'=>$orderPayment->id,
                'user_id'=>auth()->id(),
                'coupon_id'=>$request->coupon_id,
                'order'=>$request->order,
                'delivery_fee'=>$request->delivery_fee,
                'total'=>$request->total,
                'order_type'=>$request->order_type,
            ];

            if (isset($request->delivery_boy_id) && !$request->delivery_boy_id == null) {
                $data['status'] = Order::$ORDER_ASSIGN_SHOP_TO_DELIVERY;
            } else {
                $data['status'] = Order::$ORDER_WAIT_FOR_CONFIRMATION;

            }

            if (isset($request->coupon_discount)) {
                $data['coupon_discount'] = $request->coupon_discount;
            }
            if($request->order_type == 'urgent'){
                $data['expedited_fees'] = $request->expedited_fees;
            }

            if (isset($request->shop_id) && !$request->shop_id == null) {
                $shop = Shop::where('id',$request->shop_id)->first();
                if(!$shop){
                    return response(['errors' => ['This shop dose not exist']], 404);
                }
                $data['shop_id'] = $shop->id;
                $data['latitude'] = $shop->latitude;
                $data['longitude'] = $shop->longitude;
                $shop_commesion = Shop::where('id' , $shop->id)->first();
                if($shop_commesion){
                    $expedited_fees = $shop_commesion->category()->first()->expedited_fees;
                }
                $revenue = $request->order;
                $admin_revenue = $expedited_fees;
                $shop_revenue = $revenue - $admin_revenue;
                $data['admin_revenue'] = $admin_revenue;
                $data['shop_revenue'] = $shop_revenue;
            }

            $data['otp'] = rand(100000,999999);
            $data['count'] = $request->count;
            $data['type'] = $request->type;

            if($request->payment_type == 1  ){
                $usageCount =0;

                if($request->carts){
                    foreach ($request->carts as $product){
                        $usageCount = $usageCount + $product['quantity'];
                    }
                }
                if( $usageCount > $walletCoupons->usage ){
                    return $this->errorResponse(trans('message.the remaining number of uses is not enough'), 400);
                }
            $data['wallet_id'] = $request->wallet_id;
            }

            $order = Order::create($data);
            if (isset($request->delivery_boy_id) && !$request->delivery_boy_id == null) {
                AssignToDelivery::create([
                    'delivery_boy_id' => $delivery->id,
                    'order_id'=>$order->id
                    ]);
                FCMController::sendMessage('New order available',$order, $delivery->fcm_token);
            }

            if($request->order_type == 'scheduled'  || $request->order_type == 'urgent'){
                $orderTimeData = [
                    'order_date'=>$request->order_date,
                    'order_time_from'=>$request->order_time_from,
                    'order_time_to'=>$request->order_time_to,
                    'order_id'=>$order->id
                ];
                OrderTime::create($orderTimeData);
            }
            if(isset($request->coupon_id)) {
                $userCouponData = [
                    'user_id'=>auth()->id(),
                    'coupon_id'=>$request->coupon_id,
                ];
                UserCoupon::create($userCouponData);
            }



            if($request->carts){
                foreach ($request->carts as $product){
                    Product::create([
                        'order_id'=>$order->id,
                        'sub_categories_id'=>$product['sub_categories_id'],
                        'quantity'=>$product['quantity'],
                        'price'=>$product['price'],
                        'total'=>$product['total'],
                    ]);
                }
            }
            if($request->payment_type == 1 ){
                $walletCoupons->update(['usage' => $walletCoupons->usage - $usageCount]);
            }

            DB::commit();

            /*  $shopManager = Manager::where('id',Shop::findorfail($order->shop_id)->manager_id)->first();
             if($shopManager)
                 FCMController::sendMessage("New Order","You have new order from ".auth()->user()->name, $shopManager->fcm_token);
             */
            $data = Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop','orderTime')->find($order->id);
             if ($data) {
                return $this->returnData('data', ['order'=>$data]);
            } else {
                return $this->errorResponse(trans('message.any-order-yet'), 200);
            }
        } else {

            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
                DB::rollBack();
                Log::info($e->getMessage());
                return ($e->getMessage());
            return response(['errors' => ['There is something wrong']], 403);
        }
    }
    public function storeDriverOrder(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'payment_type' => 'required',
            'carts' => 'required',
            'deliveryIds' => 'required',
            //'tax' => 'required',
            'delivery_fee' => 'required',
            'total' => 'required',
            //'status' => 'required',
            'order_type'=>'required',
            // 'shop_id'=>'required',
            'count' =>'required',
            'type' => 'required',
            'order_type' => 'required',
            'address_id' => 'required'
        ]);
        if($request->order_type == 'scheduled' || $request->order_type == 'urgent'){
            $this->validate($request, [
                'order_date'=> 'required',
                'order_time_from'=> 'required',
                'order_time_to'=> 'required',
                ]);
        }
        if($request->order_type == 'urgent'){
            $this->validate($request, [
                'expedited_fees'=> 'required',
                ]);
        }
        $deliveryIds = explode(',', $request->deliveryIds);
       // $deliveryIds = $request->deliveryIds;
       // return $deliveryIds;
        DB::beginTransaction();

        try{
            if($request->payment_type == 1 ){
                $walletCoupons = WalletCoupons::where('user_id',auth()->id())->where('wallet_id', $request->wallet_id)->first();
                if(!$walletCoupons){
                    return $this->errorResponse(trans('message.walletCoupons-not-found'),400);
                   }
                $wallet = $walletCoupons->wallet;
                if(!$wallet){
                    return $this->errorResponse(trans('message.wallet-not-found'),400);
                }
            }
        if(isset($request->coupon_id)){
            $couponResponse = UserCouponController::verifyCoupon(auth()->id(),$request->coupon_id);
            //return $couponResponse;
                 Log::info($couponResponse);
            if(!$couponResponse['success']) {
                return response(['errors' => [$couponResponse['error']]], 403);
            }
        }



        $orderPayment = OrderPaymentController::addPayment($request);

        if ($orderPayment) {
            $data = [
                'status'=>3,
                'address_id'=>$request->address_id,
                'order_payment_id'=>$orderPayment->id,
                'user_id'=>auth()->id(),
                'coupon_id'=>$request->coupon_id,
                'order'=>$request->order,
                'delivery_fee'=>$request->delivery_fee,
                'total'=>$request->total,
                'order_type'=>$request->order_type,
            ];

            if (isset($request->delivery_boy_id) && !$request->delivery_boy_id == null) {
                $data['delivery_boy_id'] = $request->delivery_boy_id;
                $data['status'] = 3;
            }

            if (isset($request->coupon_discount)) {
                $data['coupon_discount'] = $request->coupon_discount;
            }
            if($request->order_type == 'urgent'){
                $data['expedited_fees'] = $request->expedited_fees;
            }

            if (isset($request->shop_id) && !$request->shop_id == null) {
                $shop = Shop::where('id',$request->shop_id)->first();
                if(!$shop){
                    return response(['errors' => ['This shop dose not exist']], 404);
                }
                $data['shop_id'] = $shop->id;
                $data['latitude'] = $shop->latitude;
                $data['longitude'] = $shop->longitude;
                $shop_commesion = Shop::where('id' , $shop->id)->first();
                if($shop_commesion){
                    $expedited_fees = $shop_commesion->category()->first()->expedited_fees;
                }
                $revenue = $request->order;
                $admin_revenue = $expedited_fees;
                $shop_revenue = $revenue - $admin_revenue;
                $data['admin_revenue'] = $admin_revenue;
                $data['shop_revenue'] = $shop_revenue;
            }

            $data['otp'] = rand(100000,999999);
            $data['count'] = $request->count;
            $data['type'] = $request->type;
            if($request->payment_type == 1  ){
                $usageCount =0;

                if($request->carts){
                    foreach ($request->carts as $product){
                        $usageCount = $usageCount + $product['quantity'];
                    }
                }
                if( $usageCount > $walletCoupons->usage ){
                    return $this->errorResponse(trans('message.the remaining number of uses is not enough'), 400);
                }
            $data['wallet_id'] = $request->wallet_id;
            }
            $order = Order::create($data);



            if($request->order_type == 'scheduled'  || $request->order_type == 'urgent'){
                $orderTimeData = [
                    'order_date'=>$request->order_date,
                    'order_time_from'=>$request->order_time_from,
                    'order_time_to'=>$request->order_time_to,
                    'order_id'=>$order->id
                ];
                OrderTime::create($orderTimeData);
            }
            if(isset($request->coupon_id)) {
                $userCouponData = [
                    'user_id'=>auth()->id(),
                    'coupon_id'=>$request->coupon_id,
                ];
                UserCoupon::create($userCouponData);
            }

            if($request->carts){
                foreach ($request->carts as $product){
                    Product::create([
                        'order_id'=>$order->id,
                        'sub_categories_id'=>$product['sub_categories_id'],
                        'quantity'=>$product['quantity'],
                        'price'=>$product['price'],
                        'total'=>$product['total'],
                    ]);
                }
            }
            $deliveryIds = DeliveryBoy::whereIn('id', $deliveryIds)->get();
            //return $deliveryIds;
            foreach ($deliveryIds as $delivery) {
               // return $delivery;
                AssignToDelivery::create([
                    'delivery_boy_id' => $delivery->id,
                    'order_id'=>$order->id
                    ]);
                FCMController::sendMessage('New order available',$data, $delivery->fcm_token);
            }

            DB::commit();

            /*  $shopManager = Manager::where('id',Shop::findorfail($order->shop_id)->manager_id)->first();
             if($shopManager)
                 FCMController::sendMessage("New Order","You have new order from ".auth()->user()->name, $shopManager->fcm_token);
             */
            $data = Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop','orderTime')->find($order->id);
             if ($data) {
                return $this->returnData('data', ['order'=>$data]);
            } else {
                return $this->errorResponse(trans('message.any-order-yet'), 200);
            }
        } else {

            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
                DB::rollBack();
                Log::info($e->getMessage());
                return ($e->getMessage());
            return response(['errors' => ['There is something wrong']], 403);
        }
    }
    /* public function storeDriverOrder(Request $request)
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
                'order_time_from'=> 'required',
                'order_time_to'=> 'required',
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
                $orderTime->order_time_from = $request->order_time_from;
                $orderTime->order_time_to = $request->order_time_to;
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
            return Order::with('statu','carts', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop')->find($order->id);
        } else {
            return response(['errors' => ['There is something wrong']], 403);
        }}catch( Exception $e){
        DB::rollBack();
        return $e->getMessage();
        }
    }
 */
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
        $order =  Order::with('statu','carts', 'coupon', 'address', 'shop', 'orderPayment','deliveryBoy')
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

        $order=Order::with('statu','carts')->where('id',$request->order_id)->first();

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

    public function cancelOrder(Request $request ,$id){
        try{
            $manager = auth()->user();
            DB::beginTransaction();
            $order = $this->order->find($id);
            if ($order->status == Order::$ORDER_WAIT_FOR_CONFIRMATION) {
                $order->status = Order::$ORDER_CANCELLED_BY_USER;
                $order->cancellation_reason = $request->cancellation_reason;
                $order->save();
                DB::commit();
                return $this->returnMessage(trans('message.order_cancel_success'),204);
            } else {
                DB::rollBack();
                return $this->errorResponse(trans('message.order_cancel_error'), 400);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

}
