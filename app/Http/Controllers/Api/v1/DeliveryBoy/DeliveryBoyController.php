<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Helpers\AppSetting;
use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Http\Trait\UploadImage;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\Shop;
use App\Models\ShopRevenue;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DeliveryBoyController extends Controller
{
    use MessageTrait;
    use UploadImage;

    private $user;
    private $shop;
    private $manager;
    private $deliveryBoy;
    public function __construct(User $user , Shop $shop, DeliveryBoy $deliveryBoy, Manager $manager)
    {
        $this->user = $user;
        $this->shop = $shop;
        $this->manager = $manager;
        $this->deliveryBoy = $deliveryBoy;
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show()
    {
        $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
        if ($deliveryBoy->category->type == 'water') {
            $shop = $this->shop->find($deliveryBoy->shop->id);
            $subCategories = $shop->with('subCategory')->find(auth()->user()->id);
        }else{
            $subCategories =  $deliveryBoy->with('category','shop','subCategory')
            ->whereHas('subCategory', function ($query) {
                    $query->where('is_primary', 1);
                })
            ->find(auth()->user()->id);
        }
        return $this->returnData('data', [
            'deliveryBoy' => $subCategories
        ]);
    }

    public function edit()
    {

    }

    public function update(Request $request)
    {

    }

    public function destroy($id)
    {

    }

     public function accountComeFromReferrerLink(){
        $account = auth()->user();
        $referrer_account = $account->referrer;
        $users = $this->user->where('referrer_link', $referrer_account)->get();
        $managers = $this->manager->where('referrer_link', $referrer_account);
        $managers = $managers->with('shop')->get();
        if (!$managers->isEmpty()) {
            $managerData = [];
            foreach ($managers as $manager) {
                $managerData[] = [
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
                    'distance' => $manager->shop->distance,
                    'created_at' => $manager->shop->created_at,
                    'updated_at' => $manager->shop->updated_at,
                ];
            }
        }
        $deliveryBoyes = $this->deliveryBoy->where('referrer_link', $referrer_account)->get();

        return $this->returnData('data', [
            'users'=>$users,
            'shops'=>$managerData,
            'deliveryBoyes'=>$deliveryBoyes
        ]);
    }

    public function getTransactionOrder(){
        $deliveryBoyId = auth()->user()->id;

        $deliveryBoy = $this->deliveryBoy
            ->with([
                'orders' => function($query) {
                    $query->where('status', '=', 6);
                },
                'orders.user',
                'orders.shop',
                'orders.category',
                'orders.carts',
                'orders.coupon'
            ])
            ->find($deliveryBoyId);

        $orderTotal = $deliveryBoy->orders->sum('total');

        $deliveryBoy->setAttribute('order_total', $orderTotal);

        return $this->returnData('data', ['deliveryBoy' => $deliveryBoy]);
    }
}
