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
    public function getShop()
    {
        $deliveryBoy = auth()->user();
        $shop = Shop::find($deliveryBoy->shop_id);

        if ($shop) {
            return $this->returnData('data', ['shop'=>$shop]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }

    }

    public function index() {
        $shops = Shop::with('manager')
                ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 1);
                })->get();
        $managerData = [];

        foreach ($shops as $shop) {
            $managerData[] = [
                'id' => $shop->manager->id,
                'name_en' => $shop->manager->getTranslation('name', 'en'),
                'name_ar' => $shop->manager->getTranslation('name', 'ar'),
                'email' => $shop->manager->email,
                'mobile' => $shop->manager->mobile,
                'mobile_verified' => $shop->manager->mobile_verified,
                'avatar_url' => $shop->manager->avatar_url,
                'license' => $shop->manager->license,
                'is_approval' => $shop->manager->is_approval,
                'referrer' => $shop->manager->referrer,
                'referrer_link' => $shop->manager->referrer_link,
                'shop_name_en' => $shop->getTranslation('name', 'en'),
                'shop_name_ar' => $shop->getTranslation('name', 'ar'),
                'barcode' => $shop->barcode,
                'latitude' => $shop->latitude,
                'longitude' => $shop->longitude,
                'address' => $shop->address,
                'rating' => $shop->rating,
                'delivery_range' => $shop->delivery_range,
                'total_rating' => $shop->total_rating,
                'default_tax' => $shop->default_tax,
                'available_for_delivery' => $shop->available_for_delivery,
                'open' => $shop->open,
                'category_id' => $shop->category_id,
                'distance' => $shop->distance,
                'created_at' => $shop->created_at,
                'updated_at' => $shop->updated_at,
            ];
        }
        if (!empty($managerData)) {
            return $this->returnData('data', ['shops' => $managerData]);
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
