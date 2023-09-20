<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'mobile' => 'required|unique:delivery_boys',
                'password' => 'required',
                'category_id' => 'required',
                // 'driving_license' => 'required',
                // 'profile_img' => 'required',
                'car_number' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        if(DeliveryBoy::where('mobile','LIKE','%'.$request->mobile)->first()){
           return response(['message'=>"Number is  registered"], 200);
       }
        $deliveryBoy = new DeliveryBoy();
        $deliveryBoy->name = $request->get('name');
        $deliveryBoy->car_number = $request->get('car_number');
        // $deliveryBoy->shop_id = $request->get('shop_id');

        $deliveryBoy->mobile = $request->get('mobile');
        $deliveryBoy->password = Hash::make($request->get('password'));
        $deliveryBoy->category_id = $request->get('category_id');

        $path = $request->file('driving_license')->store('driving_license_avatars', 'public');
        $deliveryBoy->driving_license = $path;
        $avatar_url= $request->file('profile_img')->store('driver_avatars', 'public');
        $deliveryBoy->avatar_url = $avatar_url;

        if (isset($request->fcm_token)) {
            $deliveryBoy->fcm_token = $request->fcm_token;
        }

        if($request->email){
            $this->validate($request,[
                'email' => 'required|email|unique:delivery_boys',
            ]);
            $deliveryBoy->email = $request->get('email');
        }

        if($request->shop_id){
             $deliveryBoy->shop_id = $request->get('shop_id');
        }

        $deliveryBoy->mobile_verified = 1;
        $deliveryBoy->is_offline = 1;

        $deliveryBoy->save();
        $accessToken = $deliveryBoy->createToken('authToken')->accessToken;

        return $deliveryBoy;
        $deliveryBoy_new = DeliveryBoy::where('id',$deliveryBoy->id)->first();
        $shop_type = $deliveryBoy_new->category->title;
        if(!$shop_type){
            $shop_type = '';
        }
        return response(['delivery_boy' => $deliveryBoy_new,'type'=>$shop_type, 'token' => $accessToken]);
    }

    public function login(Request $request)
    {

        //sleep(3);

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);


        if (!DeliveryBoy::where('mobile', '=', $request->email)->exists()) {
            return response(['errors' => ['This email is not found']], 402);
        }

        $deliveryBoy = DeliveryBoy::where('mobile', $request->email)->first();

        $shop_type = $deliveryBoy->category->title;
        if(!$shop_type){
            $shop_type = '';
        }
        if ($deliveryBoy && Hash::check($request->password, $deliveryBoy->password)) {
            if(!$deliveryBoy->is_verified){
                 return response(['errors' => [trans('admin.wait_verification')]], 402);
            }
            $accessToken = $deliveryBoy->createToken('authToken')->accessToken;
            if (isset($request->fcm_token)) {
                $deliveryBoy->fcm_token = $request->fcm_token;
            }
            $deliveryBoy->save();
            return response(['delivery_boy' => $deliveryBoy, 'type'=>$shop_type, 'token' => $accessToken], 200);
        } else {
            return response(['errors' => ['Password is not correct']], 402);
        }
    }

    public function changeStatus(Request $request)
    {
        $this->validate($request, [
            'is_offline' => 'required'
        ]);
        $deliveryBoy = DeliveryBoy::find(auth()->user()->id);
        if ($request->is_offline) {
            if ($deliveryBoy->is_free) {
                $deliveryBoy->is_offline = $request->is_offline;
            } else {
                return response(['errors' => ['Please delivered current order then you can goes to offline']], 402);
            }
        } else {
            $deliveryBoy->is_offline = $request->is_offline;
        }

        if ($deliveryBoy->save()) {
            return response(['message' => ['Your status has been changed'], 'delivery_boy' => $deliveryBoy], 200);
        } else {
            return response(['errors' => ['Something went wrong']], 402);
        }
    }

    public function updateProfile(Request $request)
    {
        // return response(['errors' => ['This is demo version' ]], 403);
        $deliveryBoy =  DeliveryBoy::find(auth()->user()->id);

        if (isset($request->mobile)) {
            $deliveryBoy->mobile = $request->mobile;
        }

        if (isset($request->password)) {
            $deliveryBoy->password = Hash::make($request->password);
        }

        if (isset($request->avatar_image)) {
            $url = "storage/delivery_boy_avatars/" . Str::random(10) . ".jpg";
            $oldImage = $deliveryBoy->avatar_url;
            $data = base64_decode($request->avatar_image);
            Storage::disk('public')->put($url, $data);
            Storage::disk('public')->delete($oldImage);
            $deliveryBoy->avatar_url = $url;
        }

        if ($deliveryBoy->save()) {
            return response(['message' => ['Your setting has been changed'], 'delivery_boy' => $deliveryBoy], 200);
        } else {
            return response(['errors' => ['There is something wrong']], 402);
        }
    }

    public function verifyMobileNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        if (DeliveryBoy::where('mobile', $request->mobile)->exists()) {
            return response(['errors' => ['Mobile number already exists']], 402);
        } else {
            return response(['message' => ['You can verify with this mobile']]);
        }
    }

    public function mobileVerified(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $user =  auth()->user();


        $user->mobile = $request->get('mobile');
        $user->mobile_verified = true;


        if ($user->save()) {
            return response(['message' => ['Your setting has been changed'], 'delivery_boy' => $user], 200);
        } else {
            return response(['errors' => ['There is something wrong']], 402);
        }
    }
}
