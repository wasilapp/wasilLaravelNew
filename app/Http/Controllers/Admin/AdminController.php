<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin;
use App\Models\Manager;
use App\Models\Privacy;

use App\Models\DeliveryBoy;
use App\Models\ShopRevenue;
use App\Models\AdminRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Trait\UploadImage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class AdminController extends Controller
{
    use UploadImage;
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){

            $adminRevenues = AdminRevenue::all();
            $productsCount=0;
            $revenue=0;
            foreach ($adminRevenues as $adminRevenue) {
                $revenue += $adminRevenue->revenue;
            }


            $xAxis = [];
            $ordersCountData = [];
            $revenueCountData = [];
            for($i=6;$i>=0;$i--){
                $singleOrderCountData=0;
                $singleRevenueCountData =0;

                $carbonDate = Carbon::today()->subDays($i)->toDateString();
                array_push($xAxis,Carbon::today()->subDays($i)->shortDayName);
                $dateAdminRevenue = AdminRevenue::whereDate('created_at', '=', $carbonDate)->get();
                foreach ($dateAdminRevenue as $singleRevenue){
                    $singleOrderCountData++;
                    $singleRevenueCountData+=$singleRevenue->revenue;
                }
                array_push($ordersCountData,$singleOrderCountData);
                array_push($revenueCountData,$singleRevenueCountData);
            }

            $totalWeeklyOrders = 0;
            $totalWeeklyRevenue = 0;

            for($i=0;$i<7;$i++){
                $totalWeeklyOrders += $ordersCountData[$i];
                $totalWeeklyRevenue+= $revenueCountData[$i];
            }

            $chart = new LarapexChart();

            $chart->setType('line')
                ->setXAxis($xAxis)
                ->setDataset([
                    [
                        'name'  =>  'Orders',
                        'data'  =>  $ordersCountData
                    ],
                    [
                        'name'  =>  'Revenues',
                        'data'  =>  $revenueCountData
                    ],

                ]);

            $deliveryBoys = DeliveryBoy::all()->count();

            return view('admin.dashboard')->with([
                'products_count' => $productsCount,
                'revenue' => $revenue,
                'orders_count'=> $adminRevenues->count(),
                'chart'=>$chart,
                'total_weekly_orders'=>$totalWeeklyOrders,
                'total_weekly_revenue'=>$totalWeeklyRevenue,
                'total_delivery_boys'=>$deliveryBoys
            ]);

        }

    public function edit(){
        $id = auth()->user()->id;
        $admin = Admin::findorfail($id);

        return view('admin.auth.setting', [
            'admin' => $admin
        ]);
    }

    public function update(AdminRequest $request)
    {
        //dd($request->all());
        $id = auth()->user()->id;

        $admin = Admin::findorfail($id);
        //dd($request->type);
        try {
            DB::beginTransaction ();

            $data = [
                'name' => $request->name,
            ];

            if(isset($request->password)){
                $data['password'] = Hash::make($request->password);
            }
            if ($request->hasFile('image')) {
                //dd($request->image);
                $data['avatar_url']  =  $this->upload($request->image, 'admin_avatars');
             }

           // dd($data);
            $admin->update($data);
            DB::commit();
            return redirect()->back()->with('message', 'Profile updated');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
              return $e->getMessage();
           return redirect()->back()->with('error', 'Profile not updated');
        }

    }

    public function updateLocale($langCode){
        $admin = Admin::find(auth()->user()->id);
        $admin->locale = $langCode;
        if($admin->save()){
            return redirect()->back()->with([
                'message' => 'Language changed'
            ]);
        }else{
            return redirect()->back()->with([
                'error' => 'Something wrong'
            ]);
        }

    }

    public function create_privacy(){

         $privacy = Privacy::first();


        return view('admin.privacy',compact(['privacy']));

    }

    public function updatePrivacy (Request $request){

        $privacy = Privacy::first();
        if($privacy){

        $privacy->update(['title' => $request->title]);
        }else{
            Privacy::create([
                'title' => $request->title
                ]);
        }
            return redirect()->back();
    }

}
