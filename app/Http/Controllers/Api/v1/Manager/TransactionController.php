<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Helpers\AppSetting;
use App\Http\Trait\MessageTrait;
use App\Models\DeliveryBoy;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Shop;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;

class TransactionController extends Controller
{
    use MessageTrait;
    private $shop;
    private $order;
    private $deliveryBoy;
    public function __construct(DeliveryBoy $deliveryBoy,Order $order, Shop $shop)
    {
        $this->shop = $shop;
        $this->order = $order;
        $this->deliveryBoy = $deliveryBoy;
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        if ($shop) {
            $transaction = Transaction::where('shop_id','=',$shop->id)->get();
            return $this->returnData('data', ['transaction' => $transaction]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }
    }

    public function showWithDriver($id)
    {
        $shop = auth()->user()->shop;
        $deliveryBoy_id = $this->deliveryBoy->find($id);
        if ($shop) {
            $transaction = Transaction::where('shop_id','=',$shop->id)->where('delivery_boy_id','=',$deliveryBoy_id->id)->get();
            return $this->returnData('data', ['transaction' => $transaction]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }
    }

    public function getTransactionDeliveryBoysOrdersTotal(){
        $shop =  auth()->user()->shop;

        if($shop) {
            $shopDeliveryBoys = $this->deliveryBoy->where('shop_id', '=', $shop->id)
            ->where('is_approval', '=', 2)
            ->where('category_id', '=', 1)
            ->with(['subCategory','shop','category','orders' => function ($query) {
                    $query->where('status', '=', 6);
                },'orders.user','orders.carts','orders.coupon','orders.orderTime','orders.orderPayment'=> function ($query) {
                    $query->where('payment_type', '=', 2);
                }])->get();
            foreach ($shopDeliveryBoys as $deliveryBoy) {
                $orderTotalAmount = 0;
                $orderPaidAmount = 0;
                $RemainingAmount = 0;
                $orders_driver = $deliveryBoy->orders->where('status', '=', 6);
                foreach($orders_driver as $order){
                    $orderTotalAmount += $order->total;
                    if ($order->is_paid == 1) {
                        $orderPaidAmount += $order->total;
                    }
                }
                $RemainingAmount = $orderTotalAmount - $orderPaidAmount;
                $deliveryBoy->setAttribute('order_total_amount', $orderTotalAmount);
                $deliveryBoy->setAttribute('paid_amount', $orderPaidAmount);
                $deliveryBoy->setAttribute('remaining_amount', $RemainingAmount);
            }
            return $this->returnData('data', [
                'shopDeliveryBoys'=>$shopDeliveryBoys
            ]);
        }
    }

    public function getOrdersDateNotPaidCache($id,Request $request){
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $deliveryBoy = $this->deliveryBoy->with([
                'orders' => function($query) use ($startDate, $endDate) {
                    $query->where('status', '=', 6);
                    $query->where('is_paid', '=', 0);
                    $query->join('order_times', 'orders.id', '=', 'order_times.order_id');
               //     $query->where('order_times.id', '=', 1);
                    $query->whereBetween('order_times.order_date', [$startDate, $endDate]);
                },
                'orders.carts',
                'orders.orderTime',
                'orders.orderPayment'=> function ($query) {
                    $query->where('payment_type', '=', 2);
                }])
                ->find($id);

        $orderTotal = $deliveryBoy->orders->sum('total');

        $deliveryBoy->setAttribute('orders_total', $orderTotal);

        return $this->returnData('data', ['deliveryBoy' => $deliveryBoy]);
    }

    public function paidOrders(Request $request,$id)
    {
        try {
            $validator = Validator::make($request->all(),[
                'total' => 'required',
                'from_date' => 'required|string',
                'to_date' => 'required|string',
                'orders' => 'required|array',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $shop_id = auth()->user()->id;
            $orderIds = $request->get('orders');

            if (empty($orderIds)) {
                return response()->json(['message' => trans('message.no_orders') ]);
            }

            Order::whereIn('id', $orderIds)->update(['is_paid' => 1]);

            $data = [
                'shop_id' => $shop_id,
                'delivery_boy_id' => $id,
                'total' => $request->get('total'),
                'from_date' => $request->get('from_date'),
                'to_date' => $request->get('to_date'),
                'time' => $request->get('time'),
            ];

            $transaction = Transaction::create($data);

            DB::commit();
            return $this->returnDataMessage('data', ['transaction'=>$transaction], trans('message.transaction_created'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
