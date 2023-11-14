<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Helpers\AppSetting;
use App\Http\Controllers\Controller;
use App\Models\ShopRevenue;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\Shop;
use App\Http\Trait\MessageTrait;

class ManagerController extends Controller
{
    use MessageTrait;

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

    public function index()
    {

        $shop = Manager::find(auth()->user()->id)->shop;
        if ($shop) {
            $shopRevenues = ShopRevenue::where('shop_id','=',$shop->id)->get();
            $productsCount=0;
            $revenue=0;
            foreach ($shopRevenues as $shopRevenue) {
                $productsCount += $shopRevenue->products_count;
                $revenue += $shopRevenue->revenue;
            }


            $productsCountData = [];
            $ordersCountData = [];
            $revenueCountData = [];
            for($i=6;$i>=0;$i--){
                $singleProductsCountData=0;
                $singleOrderCountData=0;
                $singleRevenueCountData =0;

                $carbonDate = Carbon::today()->subDays($i)->toDateString();
                $dateShopRevenue = ShopRevenue::whereDate('created_at', '=', $carbonDate)->where('shop_id','=',$shop->id)->get();
                foreach ($dateShopRevenue as $singleRevenue){
                    $singleOrderCountData++;
                    $singleProductsCountData+=$singleRevenue->products_count;
                    $singleRevenueCountData+=$singleRevenue->revenue;
                }
                array_push($productsCountData,$singleProductsCountData);
                array_push($ordersCountData,$singleOrderCountData);
                array_push($revenueCountData,$singleRevenueCountData);
            }

            $totalWeeklyProducts = 0;
            $totalWeeklyOrders = 0;
            $totalWeeklyRevenue = 0;

            for($i=0;$i<7;$i++){
                $totalWeeklyProducts += $productsCountData[$i];
                $totalWeeklyOrders += $ordersCountData[$i];
                $totalWeeklyRevenue += $revenueCountData[$i];
            }

            return response(['products_count' => $productsCount,
                'revenue' => $revenue,
                'orders_count'=> $shopRevenues->count(),
                'products_count_data'=>$productsCountData,
                'orders_count_data'=>$ordersCountData,
                'revenue_count_data'=>$revenueCountData,
                'total_weekly_products'=>$totalWeeklyProducts,
                'total_weekly_orders'=>$totalWeeklyOrders,
                'total_weekly_revenue'=>$totalWeeklyRevenue]);
        }
        else {
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


}
