<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use App\Http\Controllers\Controller;
use App\Models\Manager;

class CategoryController extends Controller
{
    use MessageTrait;
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    public function index(Request $request)
    {
        $categories = Category::get();

        if ($categories) {
            return $this->returnData('data', ['categories'=>$categories]);
        } else {
            return $this->errorResponse(trans('message.any-Categories-yet'), 200);
        }
    }

    public function create()
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

    public function getProducts($id): array
    {
        $user_id = auth()->user()->id;
        $products = Product::with('productImages','productItems','productItems.productItemFeatures')->where('active','=',true)->where('category_id',$id)->get();
        $filterProduct = [];
        foreach ($products as $product){
            if(Favorite::where('user_id','=',$user_id)->where('product_id','=',$product['id'])->exists()){
                $product['is_favorite'] = true;
            }else{
                $product['is_favorite'] = false;
            }
            array_push($filterProduct,$product);
        }
        return $filterProduct;
    }

    public function getShops($id): array
    {

        $shops = Shop::where('category_id',$id)->get();
        $filtershop = [];

        foreach($shops as $shop){
           array_push($filtershop,$shop);
       }
        return $filtershop;
   }

   public function getSubcategories($id): array
    {
        $subcategories = SubCategory::where('category_id',$id)->where('active', 1)->where('is_approval', 1)->get();
        $filtersubcategory = [];
        foreach($subcategories as $subcategory){
           array_push($filtersubcategory,$subcategory);
        }
        return $filtersubcategory;
        // if ($filtersubcategory) {
        //     return $this->returnData('data', ['filtersubcategory'=>$filtersubcategory]);
        // } else {
        //     return $this->errorResponse(trans('message.any-subcategories-yet'), 204);
        // }
   }

    public function getByShopOrDeliveryBoyLocation(Request $request,$category_id){

        $account = auth()->user();
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($category_id == 2){
            if ($account->referrer_link != null) {
                $referrerLink = $account->referrer_link;
                $parts = explode('=', $referrerLink);
                $variableName = $parts[0];
                $idValue = $parts[1];
            }
            if ($account->referrer_link != null && $variableName === 'driver_id') {
                $referrerDeliveryBoy =  DeliveryBoy::where('referrer', $referrerLink)
                    ->where('is_approval', 2);
                $deliveryBoy = $referrerDeliveryBoy->get();
                if (!$deliveryBoy->isEmpty()) {
                    return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy]);
                } else {
                    return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy],trans('message.any-delivery-boy-yet'));
                }
            }else{
                $query = DeliveryBoy::query();
                $query->where('is_approval', 2);
                if (!empty($request->shop_id)) {
                    $query->where('shop_id', $request->shop_id);
                }

                $query->where('category_id', $category_id);

                if ($latitude && $longitude) {
                    $query->selectRaw(
                        '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                        [$latitude, $longitude, $latitude]
                    );

                    $query->whereRaw('distance <= delivery_boys.distance');
                    $query->orderByRaw('distance ASC');
                }

                if ($request->input('search')) {
                    $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
                }

                $deliveryBoy = $query->get();

                if (!$deliveryBoy->isEmpty()) {
                    return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy]);
                } else {
                    return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy],trans('message.any-delivery-boy-yet'));
                }
            }
        } else if($category_id == 1){
            if ($account->referrer_link != null) {
                $referrerLink = $account->referrer_link;
                $parts = explode('=', $referrerLink);
                $variableName = $parts[0];
                $idValue = $parts[1];
            }
            if ($account->referrer_link != null && $variableName === 'shop_id') {
                $referrerShop =  Manager::where('referrer', $referrerLink);
                $manager = $referrerShop->with('shop')->first();
                $shop_id = $manager->shop->id;
                $shop = Shop::with('manager')
                ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 1);
                })->find($shop_id);
                $managerData = [];
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
                if ($shop) {
                    return $this->returnData('data', ['shops'=>$managerData]);
                } else {
                    return $this->returnDataMessage('data', ['shops'=>$managerData],trans('message.any-shop-yet'));
                }
            }else{
                $query = Shop::with('manager')
                    ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 1);
                });

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
                if (!$shops->isEmpty()) {
                    return $this->returnData('data', ['shops'=>$managerData]);
                } else {
                    return $this->errorResponse(trans('message.any-shop-yet'), 200);
                }
            }

        } else {
            return $this->errorResponse(trans('message.category-not-found'),400);
        }
    }

}
