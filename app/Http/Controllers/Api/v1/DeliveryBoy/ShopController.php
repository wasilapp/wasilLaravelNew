<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Models\Shop;
use App\Models\Coupon;
use App\Models\Favorite;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    use MessageTrait;
    public function index()
    {
        $deliveryBoy = auth()->user();
        $shop = Shop::find($deliveryBoy->shop_id);

        if ($shop) {
            return $this->returnData('data', ['shop'=>$shop]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
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

    }


    public function edit($id)
    {

    }


    public function update(Request $request)
    {

    }


    public function destroy($id){

    }


}
