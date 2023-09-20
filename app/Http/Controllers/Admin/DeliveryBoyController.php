<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyRevenue;
use App\Models\DeliveryBoyReview;
use App\Models\Manager;
use App\Models\Shop;
use App\Models\ShopRevenue;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Trait\UploadImage;

class DeliveryBoyController extends Controller
{

    use UploadImage;
    private $shop;
    private $shopRevenue;
    private $category;
    private $Manager;
    private $deliveryBoy;
    private $deliveryBoyRevenue;
    private $deliveryBoyReview;
    public function __construct(Shop $shop,ShopRevenue $shopRevenue,Category $category,Manager $Manager,
        DeliveryBoy $deliveryBoy,DeliveryBoyRevenue $deliveryBoyRevenue,DeliveryBoyReview $deliveryBoyReview)
    {
        $this->shop = $shop;
        $this->shopRevenue = $shopRevenue;
        $this->shopRevenue = $shopRevenue;
        $this->category = $category;
        $this->Manager = $Manager;
        $this->deliveryBoy = $deliveryBoy;
        $this->deliveryBoyRevenue = $deliveryBoyRevenue;
        $this->deliveryBoyReview = $deliveryBoyReview;
    }

    public function index()
    {
        $deliveryBoys = $this->deliveryBoy->paginate(10);

        foreach ($deliveryBoys as $deliveryBoy) {
            $ordersCount=0;
            $revenue=0;
            $deliveryBoyRevenues = $this->deliveryBoyRevenue->where('delivery_boy_id','=',$deliveryBoy->id)->get();
            foreach ($deliveryBoyRevenues as $deliveryBoyRevenue) {
                $ordersCount += 1;
                $revenue += $deliveryBoyRevenue->revenue;
            }
            $deliveryBoy['revenue']=$revenue;
            $deliveryBoy['orders_count']=$ordersCount;
        }

        return view('admin.delivery-boy.delivery-boys')->with([
            'delivery_boys'=>$deliveryBoys
        ]);
    }


    public function create()
    {
        $categories = $this->category->where('active',1)->get();

        return view('admin.delivery-boy.create-delivery-boys',compact('categories'));
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction ();
            if($this->deliveryBoy->where('mobile','LIKE','%'.$request->mobile)->first()){
                return response(['message'=>"Number is  registered"], 200);
            }
            if ($request->has('driving_license')) {
               $driving_license  =  $this->upload($request->driving_license,'driving_license_avatars');
            }
            if ($request->has('avatar_url')) {
               $avatar_url  =  $this->upload($request->avatar_url,'avatar_url');
            }
            if($request->has('shop_id')){
                $shop_id = $request->get('shop_id');
            }
            $deliveryBoyData = [
                'name' => [
                    'en' => $request->input('name')['en'],
                    'ar' => $request->input('name')['ar']
                ],
                'mobile' =>  "+962". $request->input ('mobile'),
                'car_number' => $request->input ('car_number'),
                'email' => $request->input ('email'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $request->input ('category_id'),
                'driving_license' => $driving_license,
                'avatar_url' => $avatar_url,
                'shop_id' => $shop_id,
                'is_verified' => 1,
                'mobile_verified' => 1,
                'is_offline' => 0,
            ];
            //  $str =$request->get('mobile');
            // $deliveryBoy = new DeliveryBoy();
            // $deliveryBoy->name = $request->get('name');
            // $deliveryBoy->car_number = $request->get('car_number');
            // $deliveryBoy->mobile = "+962".$str;
            // $deliveryBoy->password = Hash::make($request->get('password'));
            // $deliveryBoy->category_id = $request->get('category_id');
            // $path = $request->file('driving_license')->store('driving_license_avatars', 'public');
            // $avatar_url= $request->file('profile_pic')->store('driver_avatars', 'public');
            // $deliveryBoy->driving_license = $path;
            // $deliveryBoy->is_verified = 1;
            // if($request->email){
            //     $this->validate($request,[
            //         'email' => 'required|email|unique:delivery_boys',
            //     ]);
            //     $deliveryBoy->email = $request->get('email');
            // }
            // if($request->shop_id){
            //     $deliveryBoy->shop_id = $request->get('shop_id');
            // }

            // $deliveryBoy->avatar_url = $avatar_url;
            // $deliveryBoy->mobile_verified = 1;
            // if($deliveryBoy->save()){
            //     return redirect()->route('admin.delivery-boys.index')->with('success','Delivery added successfully');

            // }
            $this->deliveryBoy->create($deliveryBoyData);
            DB::commit();
             return redirect()->route('admin.delivery-boys.index')->with(['message' => 'Delivery has been created']);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $e->getMessage();
            return redirect()->route('admin.delivery-boys.index')->with(['error' => 'Something wrong']);
        }
        // $this->validate(
        //     $request,
        //     [
        //         'name' => 'required',//
        //         'mobile' => 'required',//
        //         'password' => 'required',
        //         'category_id' => 'required',
        //         'driving_license' => 'required',
        //         'profile_pic' => 'required',
        //         'car_number' => 'required'
        //     ]
        // );

    }

    public function show($id)
    {

        $deliveryBoy = $this->deliveryBoy->where('id',$id)->first();
           return view('admin.delivery-boy.manage-delivery-boys')->with([
            'delivery_boy'=>$deliveryBoy
        ]);
    }


    public function update(Request $request, $id){
        // dd($request->all());
        $this->validate(
            $request,
            [
                'name' => 'required',//
                'mobile' => 'required',//
                'password' => 'required',
                'category_id' => 'required',
                'driving_license' => 'required',
                'profile_pic' => 'required',
                'car_number' => 'required',
                // 'shop_id'=>'required'
            ]
        );

           if($this->deliveryBoy->where('mobile','LIKE','%'.$request->mobile)->first()){
           return response(['message'=>"Number is  registered"], 200);

       }

        $str =$request->get('mobile');
        $deliveryBoy = new DeliveryBoy();
        $deliveryBoy->name = $request->get('name');
        // $deliveryBoy->shop_id = $request->get('shop_id');
        $deliveryBoy->car_number = $request->get('car_number');
        $deliveryBoy->mobile = "+962".$str;
        $deliveryBoy->password = Hash::make($request->get('password'));
        $deliveryBoy->category_id = $request->get('category_id');
        $path = $request->file('driving_license')->store('driving_license_avatars', 'public');
        $avatar_url= $request->file('profile_pic')->store('driver_avatars', 'public');
        $deliveryBoy->driving_license = $path;
        $deliveryBoy->is_verified = 1;
        if($request->email){
            $this->validate($request,[
                'email' => 'required|email|unique:delivery_boys',
            ]);
            $deliveryBoy->email = $request->get('email');
        }

        if($request->shop_id){
            $deliveryBoy->shop_id = $request->get('shop_id');
        }
        $deliveryBoy->avatar_url = $avatar_url;
        $deliveryBoy->mobile_verified = 1;
        if($deliveryBoy->save()){
            return redirect()->route('admin.delivery-boys.index')->with('success','Delivery added successfully');

        }
        return redirect()->back()->with('faild','Something wrong');

    }


    public function destroy($id)
    {
        try {
           $deliveryBoy = $this->deliveryBoy->find($id);

            if (!$deliveryBoy) {
                return response()->json(['message' => 'DeliveryBoy not found'], 404);
            }

            $deliveryBoy->orders()->delete();
            $deliveryBoy->transactions()->delete();
            if ($deliveryBoy->avatar_url) {
                Storage::disk('public')->delete($deliveryBoy->avatar_url);
            }

            if ($deliveryBoy->driving_license_url) {
                Storage::disk('public')->delete($deliveryBoy->driving_license_url);
            }
            $deliveryBoy->delete();

            return redirect(route('admin.delivery-boys.index'))->with([
                    'message' => 'DeliveryBoy Deleted'
                ]);
        } catch (\Exception $e) {
            // return $e;
            return redirect()->back()->with([
                 'error' => $e->getMessage()
            ]);
        }

    }


    public function showReviews($id){

         $deliveryBoyReviews =  $this->deliveryBoyReview->with('user')->where('delivery_boy_id','=',$id)->get();

         return view('admin.delivery-boy.show-reviews-delivery-boy')->with([
             'deliveryBoyReviews'=>$deliveryBoyReviews
         ]);

    }

}
