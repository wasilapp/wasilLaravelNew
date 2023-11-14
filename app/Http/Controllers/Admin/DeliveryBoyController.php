<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Trait\UploadImage;
use App\Models\Banner;
use App\Models\Category;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyRevenue;
use App\Models\DeliveryBoyReview;
use App\Models\Manager;
use App\Models\Shop;
use App\Models\ShopRevenue;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        $deliveryBoys = $this->deliveryBoy->where('is_approval', 2)->paginate(10);

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
    public function deliveryBoyRequest()
    {
        $deliveryBoys = $this->deliveryBoy->where('is_approval', "!=", 2)->paginate(10);

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

        return view('admin.delivery-boy.delivery-boys-request')->with([
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
            $validator = Validator::make($request->all(),[
                'name.en' => 'required',
                'name.ar' => 'required',
                'mobile' => 'required|string|unique:delivery_boys',
                'email' => 'required|email|unique:delivery_boys',
                'distance' => 'integer|min:10',
                'password' => 'required|string|min:8',
                'category_id' => 'required',
                'car_number' => 'required',
                'driving_license' => 'required',
                'avatar_url' => 'required',
                'shop_id' => ($request->input('category_id') == 1) ? 'required' : 'nullable',
                'agency_name' => ($request->input('category_id') == 2) ? 'required' : 'nullable'
            ]);
            if ($validator->fails())
            {
                return redirect()->route('admin.delivery-boy.create-delivery-boys')->with(['error' => $validator->errors()->all()]);
            }
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
            if($request->has('distance')){
                $distance = $request->get('distance');
            }
            if($request->has('full_gas_bottles')){
                $full_gas_bottles = $request->get('full_gas_bottles');
            }
            $deliveryBoyData = [
                'name' => [
                    'en' => $request->input('name')['en'],
                    'ar' => $request->input('name')['ar']
                ],
                'agency_name' => [
                    'en' => $request->input('agency_name')['en'],
                    'ar' => $request->input('agency_name')['ar']
                ],
                'mobile' =>  "962". $request->input ('mobile'),
                'car_number' => $request->input ('car_number'),
                'email' => $request->input ('email'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $request->input ('category_id'),
                'latitude' => $request->input ('latitude'),
                'longitude' => $request->input ('longitude'),
                'driving_license' => $driving_license,
                'avatar_url' => $avatar_url,
                'shop_id' => $shop_id,
                'distance' => $distance,
                'full_gas_bottles' => $full_gas_bottles,
                'is_verified' => 1,
                'mobile_verified' => 1,
                'is_offline' => 0,
                'is_approval' => 2
            ];

            $this->deliveryBoy->create($deliveryBoyData);
            DB::commit();
             return redirect()->route('admin.delivery-boys.index')->with(['message' => 'Delivery has been created']);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.delivery-boys.index')->with(['error' => 'Something wrong']);
        }

    }

    public function show($id)
    {
        $deliveryBoy = $this->deliveryBoy->find($id);
        return view('admin.delivery-boy.show-delivery-boy')->with([
            'delivery_boy'=>$deliveryBoy
        ]);
    }

    public function edit($id)
    {
        $deliveryBoy = $this->deliveryBoy->find($id);
        $categories = $this->category->where('active',1)->get();
        return view('admin.delivery-boy.edit-delivery-boy')->with([
            'delivery_boy'=>$deliveryBoy,
            'categories'=>$categories
        ]);
    }

    public function update(Request $request, $id){
        try {

            DB::commit();
            return redirect()->route('admin.delivery-boys.index')->with(['message' => 'Delivery has been created']);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.delivery-boys.index')->with(['error' => 'Something wrong']);
        }

        $validator = Validator::make($request->all(),[
                'name' => 'required',
                'mobile' => 'required',
                'driving_license' => 'required',
                'avatar_url' => 'required',
                'car_number' => 'required',
                'car_number' => 'required',
                'distance' => 'required',
                'full_gas_bottles' => 'required'
            ]);
        if ($validator->fails())
        {
            return redirect()->route('admin.delivery-boys.edit-delivery-boy')->with(['error' => $validator->errors()->all()]);
        }
        if($this->deliveryBoy->where('mobile','LIKE','%'.$request->mobile)->first()){
           return response(['message'=>"Number is  registered"], 200);
        }
        if ($request->has('driving_license')) {
            $driving_license  =  $this->upload($request->driving_license,'driving_license_avatars');
        }
        if ($request->has('avatar_url')) {
            $avatar_url  =  $this->upload($request->avatar_url,'avatar_url');
        }
        $deliveryBoy = new DeliveryBoy();
        $deliveryBoy->name = $request->get('name');
        $deliveryBoy->car_number = $request->get('car_number');
        $deliveryBoy->mobile = "962". $request->get('mobile');
        $deliveryBoy->driving_license = $driving_license;
        $deliveryBoy->avatar_url = $avatar_url;
        if($request->email){
            $this->validate($request,[
                'email' => 'required|email|unique:delivery_boys',
            ]);
            $deliveryBoy->email = $request->get('email');
        }
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
    public function accept($id){

        $del = DeliveryBoy::findOrFail($id);
        $del->is_verified = 1;
        $del->is_approval = 2;
        $del->is_offline = 0;
        $del->save();
        return redirect()->route('admin.delivery-boys.index')->with(['success' => 'Updated']);

    }
    public function decline($id){
        $del = DeliveryBoy::findOrFail($id);
        $del->is_approval = -2;
        $del->save();
        return redirect()->route('admin.delivery-boy-request.index')->with(['success' => 'Updated']);


    }

    public function deliveryBoyRequestshow($id){
       $deliveryBoy = $this->deliveryBoy->where('id',$id)->first();
           return view('admin.delivery-boy.delivery-boys-request-show')->with([
            'delivery_boy'=>$deliveryBoy
        ]);
    }

    public function getBanners(){
        $banners = Banner::where('type','driver')->get();
        return view('admin.delivery-boy.banners.banners')->with([
            'banners' => $banners,
        ]);
    }
    public function createBanners(){
        return view('admin.delivery-boy.banners.add-banner-images');
    }
    public function storeBanners(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'url' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails())
            {
                return redirect()->route('admin.drivers-banners.create')->with(['error' => $validator->errors()->all()]);
            }
            DB::beginTransaction ();
            if ($request->has('url')) {
               $url  =  $this->upload($request->url,'url_banner');
            }

            $bannerData = [
                'url' => $url,
                'type' => $request->input ('type'),
            ];
            Banner::create($bannerData);
            DB::commit();
            return redirect()->route('admin.drivers-banners.index')->with(['message' => 'banner has been created']);
        } catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('admin.drivers-banners.create')->with(['error' => 'Something wrong']);
        }
    }

    public function destroyBanners($id){
        try {
            $banner =  Banner::findOrFail($id);
            DB::beginTransaction();
            $banner->delete();
            DB::commit();
            return redirect()->route('admin.drivers-banners.index')->with('success','Banner deleted successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.drivers-banners.index')->with(['error' => 'Banner not deleted']);
        }
    }
}
