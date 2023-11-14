<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AppSetting;
use App\Models\DeliveryBoy;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Shop;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;
use Illuminate\Support\Facades\DB;
use Exception;


class TransactionController extends Controller
{
    public function index()
    {


        $shops = Shop::all();


        $deliveryBoys = DeliveryBoy::where('shop_id',Null)->get();


        $transactions =  Transaction::paginate(10);

        return view('admin.transaction.transactions')->with([
            'transactions' => $transactions,
            'shops'=>$shops,
            'deliveryBoys'=>$deliveryBoys
        ]);
    }


    public function capturePayment($id): \Illuminate\Http\RedirectResponse
    {
        $transaction = Transaction::with('orderPayment', 'deliveryBoy')->find($id);

        if (Order::isPaymentByRazorpay($transaction['orderPayment']['payment_type'])) {

            if ($transaction['captured']) {
                return redirect()->back()->with([
                    'error' => 'Payment captured'
                ]);
            }

            $api = new Api(AppSetting::$RAZORPAY_ID, AppSetting::$RAZORPAY_SECRET);
            $payment = $api->payment->fetch($transaction['orderPayment']['payment_id']);
            try {
                $payment->capture(array('amount' => round($transaction['order']['total'] * 100), 'currency' => AppSetting::$currencyCode));

                $transaction->captured = true;
                if($transaction->save()){
                    return redirect()->back()->with([
                        'message' => 'Payment captured'
                    ]);

                }
                return redirect()->back()->with([
                    'error' => 'Something wrong'
                ]);

            } catch (BadRequestError $e) {

                return redirect()->back()->with([
                    'error' => 'Something wrong'
                ]);
            }

        } else {
            return redirect()->back()->with([
                'error' => 'This is Cash on delivery order'
            ]);
        }

    }

    public function refundPayment($id)
    {
        $transaction = Transaction::with('orderPayment', 'deliveryBoy')->find($id);

        if (Order::isPaymentByRazorpay($transaction['orderPayment']['payment_type'])) {

            if ($transaction['captured']) {
                return redirect()->back()->with([
                    'error' => 'Payment captured'
                ]);
            }
            $api = new Api(AppSetting::$RAZORPAY_ID, AppSetting::$RAZORPAY_SECRET);
            $payment = $api->payment->fetch($transaction['orderPayment']['payment_id']);
            try {
                $payment->capture(array('amount' => round($transaction['order']['total']* 100) , 'currency' => AppSetting::$currencyCode));
                $payment->refund();
                $transaction->refunded = true;
                if($transaction->save()){
                    return redirect()->back()->with([
                        'message' => 'Payment refunded'
                    ]);

                }
                return redirect()->back()->with([
                    'error' => 'Something wrong'
                ]);

            } catch (BadRequestError $e) {
                return $e;
                return redirect()->back()->with([
                    'error' => 'Something wrong'
                ]);
            }

        } else {
            return redirect()->back()->with([
                'error' => 'This is Cash on delivery order'
            ]);
        }

    }



    static function addTransaction($orderId): bool
    {

        $order = Order::find($orderId);
        $orderPayment = OrderPayment::find($order->order_payment_id);

        if(Order::isPaymentByCOD($orderPayment->payment_type) && Order::isCancelStatus($order->status)){
            return true;
        }
        $transaction = new Transaction();

        if(Order::isCancelStatus($order->status)){
            $transaction->success = false;
        }


        if(Order::isPaymentByCOD($orderPayment->payment_type)){
            if( Order::isOrderTypePickup($order->order_type)){
                $transaction->shop_to_admin = $order->admin_revenue;
            }else{
                $transaction->delivery_boy_to_admin =  $order->total -  $order->delivery_fee;
                $transaction->admin_to_shop   = $order->total -  $order->delivery_fee - $order->admin_revenue;
            }
        }else{
            if( Order::isOrderTypePickup($order->order_type)){
                $transaction->admin_to_shop = $order->total -  $order->admin_revenue;
            }else{
                $transaction->admin_to_shop = $order->total -  $order->delivery_fee - $order->admin_revenue;
                $transaction->admin_to_delivery_boy = $order->delivery_fee;
            }
        }


        $transaction->order_id = $orderId;
        $transaction->total = $order->total;
        $transaction->order_payment_id = $orderPayment->id;
        $transaction->admin_revenue = $order->admin_revenue;
        $transaction->shop_id = $order->shop_id;
        $transaction->delivery_boy_id = $order->delivery_boy_id;
        return $transaction->save();
    }


    public function store_add($shop_id, Request $req){

        //   $last_date = Transaction::where('status','paid');
        //     if($req->type == 'deliveryBoy'){
        //           $last_date->where('delivery_boy_id',$shop_id)->orderByDesc('id');
        //       }else{
        //         $last_date->where('shop_id',$shop_id)->orderByDesc('id');
        //     }

        //     $last_date = $last_date->first();
        //     if($last_date){
        //         $req->validate([
        //       'from_date' => "required|after:$last_date->to_date"],[
        //           'from_date.after' => 'Shop Paid previous orders']);
        //     }

           $req->validate([
               'from_date' => "required",
               'to_date' => 'required|after:from_date',
               'status' => 'required',
              'total' => 'required|not_in:0'
               ],
               ['total.not_in' => 'The total must be more than 0',
               'total.required' => 'Calculate Total First'
                   ]);
             DB::begintransaction();
             try{
            $transactions = new Transaction();
              if($req->type == 'deliveryBoy'){
                $transactions->delivery_boy_id = $shop_id ;
                $route = 'admin.transactions.add_delivery_transaction';
              }else{
                 $transactions->shop_id = $shop_id ;
                $route = 'admin.transactions.create';
            }
              $transactions->from_date = $req->from_date ;
              $transactions->to_date = $req->to_date ;
              $transactions->status = $req->status;
              $transactions->total = $req->total;
              $transactions->save();

            // shop transaction
            $orders = Order::whereIn('status',[5,6])->where('is_paid',0);
               if($req->type == 'shop'){
                    $orders->where('shop_id',$shop_id);
               }else{
                    $orders->where('shop_id',Null)->where('delivery_boy_id',$shop_id);
               }
                   $end_date=strtotime($req->to_date) + (24 * 60 * 60);
                   $orders->whereBetween('created_at', [date('Y-m-d', strtotime($req->from_date)) , date('Y-m-d',$end_date)])->update([
               'is_paid'=> 1
               ]);
            DB::commit();
            return redirect()->route($route,$shop_id)->with(['success' => 'Added Successfully']);
             }catch(Exception $e){
                 DB::rollBack();
              return redirect()->back()->with(['error' => 'Something wrong']);

             }
   }


    public function add($shop_id){
        $type ='shop';
        $item = Shop::findOrFail($shop_id);
        // shop transaction
        $transactions = Transaction::where('shop_id',$shop_id)->get();

       return view('admin.transaction.add-transaction')->with([
           'item'=>$item,
            'transactions' => $transactions,
            'type' =>$type,
        ]);

    }

      public function add_delivery_transaction($boy_id){
        $type ='deliveryBoy';
        $item = DeliveryBoy::findOrFail($boy_id);
        // shop transaction
        $transactions = Transaction::where('delivery_boy_id',$boy_id)->get();

       return view('admin.transaction.add-transaction')->with([
           'item'=>$item,
            'transactions' => $transactions,
            'type' =>$type,
        ]);
    }

    public function get_total($id, Request $req){
       $orders = Order::whereIn('status',[5,6]);
       if($req->type == 'shop'){
            $orders->where('shop_id',$id);
       }else{
            $orders->where('shop_id',Null)->where('delivery_boy_id',$id);
       }

       $end_date=strtotime($req->to_date) + (24 * 60 * 60);
       $total = $orders->whereBetween('created_at', [date('Y-m-d', strtotime($req->from_date)) , date('Y-m-d',$end_date)])->sum('admin_revenue');
       return $total;
    }


}
