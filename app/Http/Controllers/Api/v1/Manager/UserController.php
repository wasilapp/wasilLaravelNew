<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\DeliveryBoy;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function index(){
        $shop_id = auth()->user()->id;

       $userIdsWithOrders = $this->order->where('shop_id', $shop_id)
        ->pluck('user_id')
        ->unique();

        $users = User::whereIn('id', $userIdsWithOrders)->with('orders')->get();

        return $this->returnData('data', [
            'users'=>$users
        ]);
    }
}
