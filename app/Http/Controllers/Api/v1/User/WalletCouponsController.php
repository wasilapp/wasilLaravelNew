<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\OrderTime;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use App\Models\WalletCoupons;
use App\Http\Trait\MessageTrait;
use App\Models\AssignToDelivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WalletCouponsController extends Controller
{
    use MessageTrait;

    private $walletCoupons;
    private $wallet;
    public function __construct(WalletCoupons $walletCoupons,Wallet $wallet)
    {
        $this->walletCoupons = $walletCoupons;
        $this->wallet = $wallet;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myWalletCoupons()
    {
        try {
            $user = auth()->user();
        //    $user = User::find(auth()->user()->id);
            $walletCouponsPending = $user->orders()->where('is_wallet', 1)->where('status', '<' , 6)->with(['wallet','statu'])->get();
            // $walletCouponsAccepted = $user->orders()->where('is_wallet', 1)->where('status', '=' , 6)->with(['wallet','statu'])->get();
           // return $walletCouponsPending;

            $walletCouponsAccepted = $user->walletCoupons;
            foreach($walletCouponsAccepted as $walletCoupon){
                $walletCoupon->wallet = $walletCoupon->wallet;
            }
            return $this->returnData('data', ['walletCouponsAccepted'=>$walletCouponsAccepted, 'walletCouponsPending'=>$walletCouponsPending]);

            /* if (!$walletCouponsAccepted->isEmpty()) {
                return $this->returnData('data', ['walletCouponsAccepted'=>$walletCouponsAccepted, 'walletCouponsPending'=>$walletCouponsPending]);
            } else {
                return $this->errorResponse(trans('message.any-walletCoupons-yet'), 200);
            } */

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
        /* $coupons = WalletCoupons::where('user_id',auth()->id())->where('status',1)->whereColumn('usage' ,'<' ,'price')->orderBy('id','Desc')->get();
        $data['coupons'] = $coupons->toArray();
        $data['total'] = array_sum($coupons->pluck('price')->toArray());
        return $data; */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userCoupons()
    {
        $data = [];

        $coupons = WalletCoupons::where('user_id',auth()->id())->where('status',1)->whereColumn('usage' ,'<' ,'price')->orderBy('id','Desc')->get();
        $data['coupons'] = $coupons->toArray();
        $data['total'] = array_sum($coupons->pluck('price')->toArray());
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buyWalletCoupons1($wallet){
        try {
            $wallet = $this->wallet->where('id', $wallet)->first();
            if (!$wallet) {
                return $this->errorResponse(trans('wallet-not-found'), 400);
            }
            DB::beginTransaction ();

            $dataWalletCoupons = [
                'user_id' => auth()->user()->id,
                'wallet_id' => $wallet->id,
                'usage' => $wallet->usage,
                'price' => $wallet->price,
                'status' => 1,
                'is_paid' => 0,
            ];

            $walletCoupons = $this->walletCoupons->create($dataWalletCoupons);

            DB::commit();
            return $this->returnDataMessage('data', ['walletCoupons'=>$walletCoupons],trans('message.wallet-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = WalletCoupons::where('id',$id)->first();
        return $coupon;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request,[
            'user_number' => 'required|exists:users,id',
            'shop_number' => 'required|exists:shops,id',
            'max_usage' => 'required',
            'price'=> 'required',
            'current_usage'=> 'required',
            ]);
             // dd($request->all());
        DB::beginTransaction();
        try{
            WalletCoupons::where('id',$id)->update([
                'user_id' => $request->user_number,
                'shop_id' => $request->shop_number,
                'price' => $request->price,
                'max_usage' => $request->max_usage,
                'current_usage' => $request->current_usage,
                'status' => $request->status,
                ]);

            DB::commit();
             return redirect(route('admin.wallet-coupons.index'))->with('message', 'Coupon is updated');
        }
        catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect(route('admin.wallet-coupons.index'))->with('error', 'Coupon was not updated, Something Wrong');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function buyWalletCoupons(Request $request,$walletId)
    {
        $this->validate($request, [
            'is_wallet' => 'required',
            'delivery_fee' => 'required',
            'payment_type' => 'required',
            'total' => 'required',
            'order_type'=>'required',
            'type' => 'required',
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
        // if($request->is_wallet == '1'){
        //     $this->validate($request, [
        //         'wallet_id'=> 'required',
        //         ]);
        // }

        DB::beginTransaction();

        try{

        if(isset($request->coupon_id)){
            $couponResponse = UserCouponController::verifyCoupon(auth()->id(),$request->coupon_id);
            //return $couponResponse;
                 Log::info($couponResponse);
            if(!$couponResponse['success']) {
                return response(['errors' => [$couponResponse['error']]], 403);
            }
        }

        /* if($request->payment_type == 1 ){
             $wallet = WalletCoupons::where('user_id',auth()->id())->first();
            if($wallet){
            $wallet->update([
                'usage' => $wallet->usage + $request->order,
                ]);
            }
        } */

        $orderPayment = OrderPaymentController::addPayment($request);
        $wallet = $this->wallet->where('id', $walletId)->first();
        if (!$wallet) {
            return $this->errorResponse(trans('wallet-not-found'), 400);
        }
        if ($orderPayment) {
            $data = [
                'address_id'=>$request->address_id,
                'category_id'=>1,
                'is_wallet'=>1,
                'payment_type'=>2,
                'status'=>Order::$ORDER_WAIT_FOR_CONFIRMATION,
                'wallet_id'=>$walletId,
                'address_id'=>$request->address_id,
                'order_payment_id'=>$orderPayment->id,
                'user_id'=>auth()->id(),
                'coupon_id'=>$request->coupon_id,
                'order'=>$request->order,
                'delivery_fee'=>$request->delivery_fee,
                'total'=>$request->total,
                'order_type'=>$request->order_type,
            ];

            if (isset($request->coupon_discount)) {
                $data['coupon_discount'] = $request->coupon_discount;
            }
            if($request->order_type == 'urgent'){
                $data['expedited_fees'] = $request->expedited_fees;
            }
           // return $wallet->shop;
            $shop = $wallet->shop;
            if(!$shop){
                return response(['errors' => ['This shop dose not exist']], 404);
            }
            if($shop){

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

            DB::commit();

            $data = Order::with('statu','carts','wallet', 'coupon', 'address','deliveryBoy', 'orderPayment' ,'shop','orderTime')->find($order->id);
             if ($data) {
                return $this->returnData('data', ['order'=>$data]);
            } else {
                return $this->errorResponse(trans('message.any-order-yet'), 200);
            }
        } else {

            return response(['errors' => ['There is something wrong']], 403);
        }}catch( \Exception $e){
                DB::rollBack();
                Log::info($e->getMessage());
                return ($e->getMessage());
            return response(['errors' => ['There is something wrong']], 403);
        }
    }
}
