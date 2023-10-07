<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Helpers\DistanceUtil;
use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\Favorite;
use App\Models\Shop;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\ShopCoupon;
use App\Models\ShopSubcategory;
use App\Models\SubCategory;

class ShopController extends Controller
{

    use MessageTrait;

    public function index(Request $request)
    {
        $shops =Shop::has('manager');

        if($request->search){
            $shops->where('name', 'LIKE', '%'.$request->search.'%');
        }

        if ($shops->isEmpty()) {
            return $this->returnData('data', ['shops'=>$shops->get()]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }
    }

    public function shopLocation(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $query = Shop::has('manager');

        if ($latitude && $longitude) {
            $query->selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            );

            $query->whereRaw('distance <= shops.distance');
            $query->orderByRaw('distance ASC');
        }

        if ($request->input('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        $shops = $query->orderBy('address')->get();

        if (!$shops->isEmpty()) {
            return $this->returnData('data', ['shops'=>$shops]);
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
        $userId = auth()->user()->id;
        $shop =  Shop::with('products','manager','products.productImages','products.productItems','products.productItems.productItemFeatures')->find($id)->toArray();
        $products = $shop['products'];
        $newProducts = [];
        foreach ($products as $product){
            if(Favorite::where('user_id','=',$userId)->where('product_id','=',$product['id'])->exists()){
                $product['is_favorite'] = true;
            }else{
                $product['is_favorite'] = false;
            }
            array_push($newProducts,$product);
        }
        $shop['products'] = $newProducts;
        return $shop;

    }


    public function edit($id)
    {

    }


    public function update(Request $request)
    {
    }


    public function destroy($id){

    }

    public function getCoupons($id){
        $shopCoupons = ShopCoupon::where('shop_id','=',$id)->get();
        $coupons = [];
        foreach ($shopCoupons as $shopCoupon) {
            array_push($coupons,Coupon::find($shopCoupon->coupon_id));
        }

        return $coupons;
    }

    public function getShopsFromUserAddress($user_address_id): array
    {
        $userAddress = UserAddress::find($user_address_id);
        $allShops = Shop::has('manager')->get();

        $shops = [];
        foreach ($allShops as $shop){
            if(DistanceUtil::isValidForDelivery($shop,$userAddress)){
              array_push($shops,$shop);
            }
        }

        return $shops;
    }

    public function getSubCategoryByShop($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        $categories = $shop->subCategory;
        if ($shop) {
            return $this->returnData('data', ['shop'=>$shop]);
        } else {
            return $this->errorResponse(trans('message.any-categories-yet'), 200);
        }
    }

}
