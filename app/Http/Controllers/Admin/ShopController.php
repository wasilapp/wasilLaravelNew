<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\Manager;
use App\Models\Category;
use App\Models\ShopReview;
use App\Models\ShopRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Trait\UploadImage;
use App\Http\Requests\ShopRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Hash;
use ArielMejiaDev\LarapexCharts\LarapexChart;

use function Ramsey\Uuid\v1;

class ShopController extends Controller
{
    use UploadImage;

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
                    $query->where('is_approval', 1);
                })->paginate(10);

        foreach ($shops as $shop) {
            $productsCount = 0;
            $revenue = 0;
            $shopRevenues = $this->shopRevenue->where('shop_id', '=', $shop->id)->get();
            foreach ($shopRevenues as $shopRevenue) {
                $productsCount += $shopRevenue->products_count;
                $revenue += $shopRevenue->revenue;
            }
            $shop['revenue'] = $revenue;
            $shop['products_count'] = $productsCount;
        }

        return view('admin.shops.shops')->with([
            'shops' => $shops
        ]);
    }


    public function create()
    {
        $categories = $this->category->get();

        return view('admin.shops.create-shop')->with([
            'categories' => $categories
        ]);
    }

    public function store(ShopRequest $request)
    {
       try {
            DB::beginTransaction ();
            if($request->admin){
                if ($request->admin['avatar_url']) {
                    $avatar_url_path  =  $this->upload($request->admin['avatar_url'],'managers');
                }
                if ($request->admin['license']) {
                    $license_path  =  $this->upload($request->admin['license'],'managers');
                }
                $manger_data = [
                    'name' => [
                        'en' => $request->admin['name']['en'],
                        'ar' => $request->admin['name']['ar']
                    ],
                    'avatar_url' => $avatar_url_path,
                    'license' => $license_path,
                    'email' => $request->admin['email'],
                    'password' => Hash::make($request->admin['password']),
                    'mobile' => $request->admin['mobile'],
                    'is_approval' => 1,
                ];
                $manager = $this->manager->create($manger_data);
            }
            $data = [
                'name' => [
                    'en' => $request->shop['name']['en'],
                    'ar' => $request->shop['name']['ar']
                ],
                'category_id' => $request->shop['category'],
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'default_tax' => $request->default_tax ?? 0,
                'delivery_range' => $request->delivery_range,
                'manager_id' => $manager->id
            ];

            $number = $this->generateBarcodeNumber();

            $data['barcode'] = $number;
            if ($request->get('available_for_delivery')) {
                $data['available_for_delivery'] = true;
            } else {
                $data['available_for_delivery'] = false;
            }

            if ($request->get('open')) {
                $data['open'] = true;
            } else {
                $data['open'] = false;
            }
            $this->shop->create($data);
            DB::commit();
            return redirect()->route('admin.shops.index')->with(['message' => 'Shop has been created']);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            // return $e->getMessage();
            return redirect()->route('admin.shops.create')->with(['error' => 'Something wrong']);
        }
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
            return view('admin.shops.show-shop')->with([
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
                'redirect_url' => route('admin.shops.index')
            ]);
        }
    }

    public function edit($id)
    {
        $shop = $this->shop->with('manager')->find($id);
        $shopSubcategories = $shop->subCategory;
        $subcategories = $this->subCategory->where('category_id', $shop->category_id )->where('is_approval', 1)->get();
        //dd($subcategories );
        $available_managers = $this->manager->doesnthave('shop')->get();
        return view('admin.shops.edit-shop')->with([
            'shop' => $shop,
            'available_managers' => $available_managers,
            'subcategories' => $subcategories,
            'shopSubcategories' => $shopSubcategories,
        ]);

    } 


    public function update(Request $request)
    {
       // dd($request->all());
        $shop = $this->shop->findorFail($request->id);
        $manager = $this->manager->findorFail($shop->manager->id);
        try {
            DB::beginTransaction ();
            if($request->manager){
                $manger_data = [
                    'name' => [
                        'en' => $request->manager['name']['en'],
                        'ar' => $request->manager['name']['ar']
                    ],
                    'email' => $request->manager['email'],
                    'mobile' => $request->manager['mobile'],
                    'is_approval' => 1,
                ];
                if (isset($request->manager['avatar_url'])) {
                    $newAvatar_url = $this->updateImage($shop->manager->avatar_url,$request->manager['avatar_url'],'managers');
                    $manger_data['avatar_url']= $newAvatar_url;
                }
                if (isset($request->manager['license'])) {
                    $newLicense_url = $this->updateImage($shop->manager->license,$request->manager['license'],'managers');
                    $manger_data['license']= $newLicense_url;
                }
                if ($request->manager['password']) {
                    $manger_data['password']= Hash::make($request->admin['password']);
                }

                $manager->update($manger_data);
            }

            $data = [
                'name' => [
                    'en' => $request->shop['name']['en'],
                    'ar' => $request->shop['name']['ar']
                ],
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'default_tax' => $request->default_tax ?? 0,
                'delivery_range' => $request->delivery_range,
                'manager_id' => $manager->id
            ];

            $number = $this->generateBarcodeNumber();
            $data['barcode'] = $number;

            if ($request->get('available_for_delivery')) {
                $data['available_for_delivery'] = true;
            } else {
                $data['available_for_delivery'] = false;
            }

            if ($request->get('available_for_delivery')) {
                $data['available_for_delivery'] = true;
            } else {
                $data['available_for_delivery'] = false;
            }

            if ($request->get('open')) {
                $data['open'] = true;
            } else {
                $data['open'] = false;
            }
            
            $shop->update($data);
            
            if ($request->has('subcategories')) {
                $shop->subCategory()->sync($request['subcategories']);
            }
            DB::commit();
            return redirect()->route('admin.shops.index')->with(['message' => 'Shop has been updated']);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $e->getMessage();
            return redirect()->route('admin.shops.edit')->with(['error' => 'Something wrong']);
        }

    }

    public function destroy($id){
            $shop = $this->shop->findOrFail($id);
            DB::beginTransaction();
            try{
                $deliveries = DB::table('delivery_boys')->where('shop_id',$id);
                if(count($deliveries->get()) > 0){
                DB::table('assign_to_deliveries')->whereIn('delivery_boy_id' , [$deliveries->pluck('id')])->delete();}
                DB::table('orders')->where('shop_id',$id)->delete();
                DB::table('delivery_boys')->where('shop_id',$id)->delete();
                DB::table('managers')->where('id',$shop->manager_id)->delete();
                $shop->delete();
                DB::commit();
                return redirect(route('admin.shops.index'))->with('message', 'Shop deleted');
            }catch(\Exception $e){
                DB::rollBack();
                return redirect(route('admin.shops.index'))->with('error', 'Shop not deleted');
            }
    }

    public function showReviews($id)
    {
        $shop = $this->shop->find($id);

        if ($shop) {
            $shopReviews = $this->shopReview->with('user')->where('shop_id', '=', $shop->id)->get();
            return view('admin.shops.shop-reviews')->with([
                'shopReviews' => $shopReviews
            ]);
        } else {
            return view('admin.error-page')->with([
                'code' => 502,
                'error' => 'This shop is not available',
                'message' => 'Please go to your shop',
                'redirect_text' => 'Go to shop',
                'redirect_url' => route('admin.shops.index')
            ]);
        }
    }

    function generateBarcodeNumber() {
        $number = mt_rand(100000, 999999);

        if (Shop::where('barcode',$number)->exists()) {
            return generateBarcodeNumber();
        }
        return $number;
    }
}
