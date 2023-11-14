<?php

namespace App\Http\Controllers\Admin;
use App\Models\Shop;
use App\Models\Coupon;
use App\Models\DeliveryBoy;
use Illuminate\Http\Request;
use App\Models\DeliveryCoupon;
use App\Http\Trait\UploadImage;
use App\Models\CouponDeliveryBoy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CouponShop;

class CouponController extends Controller
{
    use UploadImage;

    private $coupon;
    private $shop;
    private $shopCoupon;
    private $deliveryCoupon;
    private $deliveryBoy;
    public function __construct(Coupon $coupon,Shop $shop,CouponShop $shopCoupon, DeliveryBoy $deliveryBoy, CouponDeliveryBoy $deliveryCoupon)
    {
        $this->coupon = $coupon;
        $this->shop = $shop;
        $this->shopCoupon = $shopCoupon;
        $this->deliveryBoy = $deliveryBoy;
        $this->deliveryCoupon = $deliveryCoupon;
    }

    public function index() 
    {
        $coupons = Coupon::orderBy('expired_at', 'ASC')->paginate(10);
        return view('admin.coupons.coupons')->with([
            'coupons' => $coupons
        ]);
    }
    public function couponsByCategory($id) 
    {
        try {
            $coupons = Coupon::where('category_id', $id)->where('is_primary', 1)->orderBy('expired_at', 'ASC')->paginate(10);

            return view('admin.coupons.coupons')->with([
                'coupons' => $coupons
            ]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    public function couponsRequestsByCategory($id) 
    {
        try {
            $coupons = Coupon::where('category_id', $id)->where('is_primary', 0)->where('is_approval', 0)->orderBy('expired_at', 'ASC')->paginate(10);

            return view('admin.coupons.requests')->with([
                'coupons' => $coupons
            ]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function create()
    {
        $shops= $this->shop->all();
        $deliveryBoys= $this->deliveryBoy->where('category_id',2)->get();
        return view('admin.coupons.create-coupon',compact('shops','deliveryBoys'));
    }

    public function store(Request $request)
    {
        try {
            //  return $request->all();
            $this->validate($request,
                [
                    'code' => 'required|unique:coupons',
                    'category_id' => 'required',
                    'description.en' => 'required',
                    'description.ar' => 'required',
                    'type' => 'required',
                    'offer' => 'required|numeric|max:100|min:0"',
                    'expired_at' => 'required|date|after:now',
                    'min_order'=>'required',
                    'max_discount'=>'required'
                ],
                [
                    'code.required' => 'Please provide coupon code',
                    'category_id.required' => 'Please provide coupon category',
                    'type.required' => 'Please provide coupon type',
                    'code.unique' => 'This coupon code has been taken, you can edit',
                    'description.en.required'=> 'Please provide en coupon description',
                    'description.ar.required'=> 'Please provide ar coupon description',
                    'offer.required'=> 'Please provide coupon offer',
                    'offer.min'=> "Please set coupon discount between 0-100%",
                    'offer.max'=> "Please set coupon discount between 0-100%",
                    'expired_at.after'=> 'The expired at must be a date after today',
                    'min_order.required'=> 'Please provide minimum order',
                    'max_discount.required'=> 'Please provide maximum discount',
                ]
            );
            DB::beginTransaction ();
        
            $data = [
                'code' => $request->code,
                'category_id' => $request->category_id,
                'offer' => $request->offer,
                'min_order' => $request->min_order,
                'max_discount' => $request->max_discount,
                'expired_at' => $request->expired_at,
                'type' => $request->type,
                'is_primary' => true,
                'is_approval' => true,
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
            ];
            
            if(isset($request->for_new_user)){
                 $data['for_new_user'] = true;
            }else{ 
                 $data['for_new_user'] = false;
            }
            
            if(isset($request->for_only_one_time)){
                $data['for_only_one_time'] = true;
            }else{
                $data['for_only_one_time'] = false;
            }
            $coupon = Coupon::create($data);
          
            ////////////////////////////////////////////////////////////////////////////////////////////
            if($request->category_id == '1'){
                
                if($request->type === 'custom'){
                    $shop = $this->shop->find($request->shop_id);

                    $shopCouponData = [
                        'shop_id' => $request->shop_id,
                        'coupon_id'=> $coupon->id
                    ];
                    $shop->coupons()->syncWithoutDetaching([ 
                        $coupon->id => $shopCouponData
                    ]);
                } 
                
                if ($request->type === 'general'){
                    
                    $shops= $this->shop->all();
                    foreach($shops as $shop){
                        $shopCouponData = [
                            'shop_id' => $shop->id,
                            'coupon_id'=> $coupon->id
                        ];
                        $shop->coupons()->syncWithoutDetaching([ 
                            $coupon->id => $shopCouponData
                        ]);
                    }
                } 
            } else {
            

                if($request->type === 'custom'){
                    $deliveryBoy = $this->deliveryBoy->find($request->deliveryBoy_id);
                   //  return $deliveryBoy;
                    
                    $deliveryCouponData = [
                        'delivery_boy_id' => $request->deliveryBoy_id,
                        'coupon_id'=> $coupon->id
                    ];
                   // return $deliveryCouponData;
                    $deliveryBoy->coupons()->syncWithoutDetaching([ 
                        $coupon->id => $deliveryCouponData
                    ]);
                    
                } 
                
                if ($request->type === 'general'){
                    $deliveryBoys= $this->deliveryBoy->where('category_id',2)->get();
                    foreach($deliveryBoys as $deliveryBoy){
                        
                        $deliveryCouponData = [
                            'delivery_boy_id' => $deliveryBoy->id,
                            'coupon_id'=> $coupon->id
                        ];
                        $deliveryBoy->coupons()->syncWithoutDetaching([ 
                            $coupon->id => $deliveryCouponData
                        ]);
                    }
                } 
            }
            
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            DB::commit();
            return redirect()->route('admin.coupons.index')->with('success','Coupon added successfully');
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
           //return $e->getMessage();
            return redirect()->route('admin.coupons.create')->with(['error' => 'Something wrong']);
        }
    }
    /* public function store(Request $request)
    {
    dd($request->all());

        $this->validate($request,
            [
                'code' => 'required|unique:coupons',
                'description' => 'required',
                'offer' => 'required|numeric|max:100|min:0"',
                'expired_at' => 'required|date|after:now',
                'min_order'=>'required',
                'max_discount'=>'required'
            ],
            [
                'code.required' => 'Please provide coupon code',
                'code.unique' => 'This coupon code has been taken, you can edit',
                'description.required'=> 'Please provide coupon description',
                'offer.required'=> 'Please provide coupon offer',
                'offer.min'=> "Please set coupon discount between 0-100%",
                'offer.max'=> "Please set coupon discount between 0-100%",
                'expired_at.after'=> 'The expired at must be a date after today',
                'min_order.required'=> 'Please provide minimum order',
                'max_discount.required'=> 'Please provide maximum discount',
            ]
        );


        $coupon = new Coupon();
        $coupon->code = $request->get('code');
        $coupon->description = $request->get('description');
        $coupon->offer = $request->get('offer');
        $coupon->min_order = $request->get('min_order');
        $coupon->max_discount = $request->get('max_discount');
        $coupon->expired_at = $request->get('expired_at');

        if(isset($request->for_new_user)){
            $coupon->for_new_user = true;
        }else{ 
            $coupon->for_new_user = false;
        }

        if(isset($request->for_new_user)){
            $coupon->for_only_one_time = true;
        }else{
            $coupon->for_only_one_time = false;
        }

        if($coupon->save()){
            return redirect(route('admin.coupons.index'))->with('message', 'Coupon added');
        }else{
            return redirect(route('admin.coupons.index'))->with('error', 'Coupon was not added');
        }
    } */

    public function show($id)
    {
        $coupon  = Coupon::find($id);

        return view('admin.coupons.show')->with([
            'coupon'=>$coupon
        ]);
    }


    public function edit($id)
    {
        $coupon  = Coupon::find($id);

        return view('admin.coupons.edit-coupon')->with([
            'coupon'=>$coupon
        ]);

    }


    public function update(Request $request,$id)
    {
        $this->validate($request,
            [
                'description' => 'required',
                'expired_at' => 'required|date|after:now'
            ],
            [
                'description.required'=> 'Please provide coupon description',
                'expired_at.after'=> 'The expired at must be a date after today'
            ]
        );

        $coupon = Coupon::find($id);
        $coupon->description = $request->get('description');
        $coupon->expired_at = $request->get('expired_at');
        if(isset($request->is_active)){
            $coupon->is_active = true;
        }else{
            $coupon->is_active = false;
        }
        if($coupon->save()){
            return redirect(route('admin.coupons.index'))->with('message', 'Coupon updated');
        }else{
            return redirect(route('admin.coupons.index'))->with('error', 'Coupon was not updated');
        }
    }


    public function destroy($id)
    {

    }

    public function accept(Request $request, $id)
    {
        $coupon = $this->coupon->findOrFail($id);
        $coupon->update(['is_approval' => 1]);
        return redirect()->route('admin.coupons.requests.index' , $coupon->category_id)->with('success','Successfully approved');
    }

    public function decline(Request $request, $id)
    {
        $coupon = $this->coupon->findOrFail($id);
        $coupon->update(['is_approval' => -1]);
        return redirect()->route('admin.coupons.requests.index' , $coupon->category_id)->with('success','Rejected successfully');
    }
}
