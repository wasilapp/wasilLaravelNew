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
        $categories = Category::with('subCategories','shops')->get();

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
        $subcategories = SubCategory::where('category_id',$id)->get();
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
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($category_id == 2){
            $query = DeliveryBoy::query();

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
                return $this->errorResponse(trans('message.any-delivery-boy-yet'),200);
            }
        } else if($category_id == 1){
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
        } else {
            return $this->errorResponse(trans('message.category-not-found'),400);
        }
    }
    
}
