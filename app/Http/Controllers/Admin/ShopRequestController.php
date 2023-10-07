<?php

namespace App\Http\Controllers\Admin;
use App\Models\Shop;
use App\Models\Manager;
use App\Models\Category;
use App\Models\ShopReview;
use App\Models\ShopRevenue;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class ShopRequestController extends Controller
{
    private $shop;
    private $manager;
    private $shopRevenue;
    private $category;
    private $shopReview;
    private $subCategory;
    public function __construct(Shop $shop, Manager $manager,ShopRevenue $shopRevenue,Category $category,ShopReview $shopReview,SubCategory $subCategory)
    {
        $this->shop = $shop;
        $this->manager = $manager;
        $this->shopRevenue = $shopRevenue;
        $this->category = $category;
        $this->shopReview = $shopReview;
        $this->subCategory = $subCategory;
    }


  

    public function index()
    {
        $shops = $this->shop->with('manager')
                ->whereHas('manager', function ($query) {
                    $query->where('is_approval', 0);
                })
                ->get();
        if($shops->count()>0){
            return view('admin.shop-requests.shop-requests')->with([
                'have_shop_request'=>true,
                'shops'=>$shops
            ]);
        }else{
            return view('admin.shop-requests.shop-requests')->with([
                'have_shop_request'=>false,
            ]);
        }


    }

    public function accept(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);
        $shop->manager->update(['is_approval' => 1]);
        return redirect()->route('admin.shop_requests.index')->with('success','Successfully approved');
    }

    public function decline(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);
        $shop->manager->update(['is_approval' => 2]);
        return redirect()->route('admin.shop_requests.index')->with('success','Rejected successfully');
    }

    public function create()
    {

    }


    public function store(Request $request)
    {
    }

    public function show($id)
    {
        $shop = $this->shop->with('manager')->find($id);
        $shopSubcategories = $shop->subCategory;

        $available_managers = $this->manager->doesnthave('shop')->get();
        if ($shop) {
            $shopRevenues = $this->shopRevenue->where('shop_id', '=', $shop->id)->get();
            $productsCount = 0;
            $revenue = 0;
            foreach ($shopRevenues as $shopRevenue) {
                $productsCount += $shopRevenue->products_count;
                $revenue += $shopRevenue->revenue;
            }
            $xAxis = [];
            $productsCountData = [];
            $ordersCountData = [];
            $revenueCountData = [];
            for ($i = 6; $i >= 0; $i--) {
                $singleProductsCountData = 0;
                $singleOrderCountData = 0;
                $singleRevenueCountData = 0;

                $carbonDate = Carbon::today()->subDays($i)->toDateString();
                array_push($xAxis, Carbon::today()->subDays($i)->shortDayName);
                $dateShopRevenue = ShopRevenue::whereDate('created_at', '=', $carbonDate)->where('shop_id', '=', $shop->id)->get();
                foreach ($dateShopRevenue as $singleRevenue) {
                    $singleOrderCountData++;
                    $singleProductsCountData += $singleRevenue->products_count;
                    $singleRevenueCountData += $singleRevenue->revenue;
                }
                array_push($productsCountData, $singleProductsCountData);
                array_push($ordersCountData, $singleOrderCountData);
                array_push($revenueCountData, $singleRevenueCountData);
            }

            $totalWeeklyProducts = 0;
            $totalWeeklyOrders = 0;
            $totalWeeklyRevenue = 0;

            for ($i = 0; $i < 7; $i++) {
                $totalWeeklyProducts += $productsCountData[$i];
                $totalWeeklyOrders += $ordersCountData[$i];
                $totalWeeklyRevenue += $revenueCountData[$i];
            }

            $chart = new LarapexChart();

            $chart->setType('line')
                ->setXAxis($xAxis)
                ->setDataset([
                    [
                        'name' => 'Products',
                        'data' => $productsCountData
                    ],
                    [
                        'name' => 'Orders',
                        'data' => $ordersCountData
                    ],
                    [
                        'name' => 'Revenues',
                        'data' => $revenueCountData
                    ],
                ]);
            return view('admin.shop-requests.show-shop')->with([
                'products_count' => $productsCount,
                'revenue' => $revenue,
                'orders_count' => $shopRevenues->count(),
                'chart' => $chart,
                'total_weekly_products' => $totalWeeklyProducts,
                'total_weekly_orders' => $totalWeeklyOrders,
                'total_weekly_revenue' => $totalWeeklyRevenue,
                'shop' => $shop,
                'available_managers' => $available_managers,
                'shopSubcategories' => $shopSubcategories
            ]);
        } else {
            return view('manager.error-page')->with([
                'code' => 502,
                'error' => 'This shop is not available',
                'message' => 'Please go to your shop and join',
                'redirect_text' => 'Go to Shop',
                'redirect_url' => route('admin.shop-requests.index')
            ]);
        }
    }


    public function edit($id)
    {

    }


    public function update(Request $request)
    {

        switch ($request->input('action')) {
            case 'accept':
                $shop = Shop::find($request->get('shop_id'));
                $shop->manager_id = $request->get('manager_id');
                if($shop->save() && DB::table('shop_requests')->where('shop_id',$request->get('shop_id'))->delete()){
                    return redirect()->back()->with([
                        'message' => 'Shop has been allocated'
                    ]);
                }else{
                    return redirect()->back()->with([
                        'error' => 'Something wrong'
                    ]);
                }
            case 'decline':
                if(DB::table('shop_requests')->where('id',$request->get('id'))->delete()){
                    return redirect()->back()->with([
                        'message' => 'Shop Request has been cancelled'
                    ]);
                }else{
                    return redirect()->back()->with([
                        'error' =>  'Something wrong'
                    ]);
                }

            default:
                return redirect()->back()->with([
                    'error' =>  'Option is wrong'
                ]);
                break;
        }


    }


    public function destroy($id){
        if(DB::table('shop_requests')->where('id',$id)->delete()){
            return redirect()->back()->with([
                'message'=>'Cancelled shop request'
            ]);
        }else{
            return redirect()->back()->with([
                'error'=>'Something wrong'
            ]);
        }
    }
}
