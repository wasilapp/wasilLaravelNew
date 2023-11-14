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
        $shops =Shop::with('manager')
                ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 1);
                })->get();

        if($request->search){
            Shop::where('name', 'LIKE', '%'.$request->search.'%');
        }
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

    public function shopLocation(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $query = Shop::with('manager')
                ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 1);
                })->where('open', 1);

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
       // return 'hi';
        $shop = Shop::findOrFail($shop_id);
        if ($shop) {
            $subCategories = $shop->subCategory()
                                ->where('is_approval', 1)
                                ->get();
            $data = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'sub_categories' => $subCategories,
            ];
            return $this->returnData('data', $data);
        } else {
            return $this->errorResponse(trans('message.any-categories-yet'), 200);
        }
    }
}
