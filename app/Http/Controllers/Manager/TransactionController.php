<?php

namespace App\Http\Controllers\Manager;

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

class    TransactionController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        if ($shop) {
        $transactions = Transaction::where('shop_id',$shop->id)->get();

        return view('manager.transaction.transactions')->with([
            'shop'=>$shop,
            'transactions' => $transactions
        ]);

        } else {
            return view('manager.error-page')->with([
                'code' => 502,
                'error' => 'You havn\'t any shop yet',
                'message' => 'Please join any shop and then manage product',
                'redirect_text' => 'Join',
                'redirect_url' => route('manager.shops.index')
            ]);
        }
    }

}
