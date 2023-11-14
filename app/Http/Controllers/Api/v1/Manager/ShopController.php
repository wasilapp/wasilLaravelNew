<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\Shop;
use App\Models\ShopReview;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;


class ShopController extends Controller
{
    use MessageTrait;

    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $manager = auth()->user();
        $managerData = [
            'id' => $manager->id,
            'name_en' => $manager->getTranslation('name', 'en'),
            'name_ar' => $manager->getTranslation('name', 'ar'),
            'email' => $manager->email,
            'mobile' => $manager->mobile,
            'mobile_verified' => $manager->mobile_verified,
            'avatar_url' => $manager->avatar_url,
            'license' => $manager->license,
            'is_approval' => $manager->is_approval,
            'referrer' => $manager->referrer,
            'referrer_link' => $manager->referrer_link,
            'shop_name_en' => $manager->shop->getTranslation('name', 'en'),
            'shop_name_ar' => $manager->shop->getTranslation('name', 'ar'),
            'barcode' => $manager->shop->barcode,
            'latitude' => $manager->shop->latitude,
            'longitude' => $manager->shop->longitude,
            'address' => $manager->shop->address,
            'rating' => $manager->shop->rating,
            'delivery_range' => $manager->shop->delivery_range,
            'total_rating' => $manager->shop->total_rating,
            'default_tax' => $manager->shop->default_tax,
            'available_for_delivery' => $manager->shop->available_for_delivery,
            'open' => $manager->shop->open,
            'category_id' => $manager->shop->category_id,
            'created_at' => $manager->shop->created_at,
            'updated_at' => $manager->shop->updated_at,
        ];
        if ($managerData) {
            return $this->returnData('data', ['shop'=>$managerData]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }

    }

    public function create()
    {

    }
// if ($shop && $manager) {
//             return $this->returnData('data', ['manager'=>$manager,'shop'=>$shop]);
//         } else {
//             return $this->errorResponse(trans('message.any-shop-yet'), 204);
//         }

    public function show($id)
    {
    }


    // public function update(Request $request, $id)
    // {
    //     $this->validate($request,
    //         [
    //             'name' => 'required',
    //             'email' => 'required|unique:shops,email,' . $id,
    //             'mobile' => 'required|unique:shops,mobile,' . $id,
    //             'description' => 'required',
    //             'address' => 'required',
    //             'latitude' => 'required',
    //             'longitude' => 'required',
    //             'default_tax' => 'required',
    //             'minimum_delivery_charge' => 'required',
    //             'delivery_cost_multiplier' => 'required',
    //             'admin_commission' => 'required',
    //             'delivery_range' => 'required',

    //         ],
    //         [

    //         ]);


    //     $shop = Shop::find($id);

    //     if (isset($request->image)) {
    //         Shop::updateShopImageWithApi($request, $id);
    //     }

    //     $shop->name = $request->get('name');
    //     $shop->email = $request->get('email');
    //     $shop->mobile = $request->get('mobile');
    //     $shop->description = $request->get('description');
    //     $shop->address = $request->get('address');
    //     $shop->latitude = $request->get('latitude');
    //     $shop->longitude = $request->get('longitude');
    //     $shop->default_tax = $request->get('default_tax');
    //     $shop->minimum_delivery_charge = $request->get('minimum_delivery_charge');
    //     $shop->delivery_cost_multiplier = $request->get('delivery_cost_multiplier');
    //     $shop->delivery_range = $request->get('delivery_range');
    //     $shop->admin_commission = $request->get('admin_commission');

    //     if ($request->get('available_for_delivery')) {
    //         $shop->available_for_delivery = true;
    //     } else {
    //         $shop->available_for_delivery = false;
    //     }

    //     if ($request->get('open')) {
    //         $shop->open = true;
    //     } else {
    //         $shop->open = false;
    //     }

    //     if ($shop->save()) {
    //         return response(['message' => ['Shop is saved']], 200);

    //     } else {
    //         return response(['errors' => ['Shop is not saved']], 403);

    //     }
    // }


    public function destroy($id)
    {

    }


    public function showReviews()
    {
        $shop = auth()->user()->shop;

        $ShopReview =  ShopReview::with('user')->where('shop_id', '=', $shop->id)->get();

        if ($shop) {
            return $this->returnData('data', ['ShopReview'=>$ShopReview]);
        } else {
            return $this->errorResponse(trans('message.any-shop-yet'), 200);
        }
    }

}
