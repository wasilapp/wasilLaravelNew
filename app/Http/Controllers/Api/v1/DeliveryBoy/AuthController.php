<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Models\Shop;
use App\Models\User;
use App\Models\DeliveryBoy;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Trait\UploadImage;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\DeliveryBoy\RegisterDeliveryBoyRequest;


class AuthController extends Controller
{
    use MessageTrait;
    use UploadImage;

    private $deliveryBoy;
    public function __construct(DeliveryBoy $deliveryBoy)
    {
        $this->deliveryBoy = $deliveryBoy;
    }
    public function register(RegisterDeliveryBoyRequest $request)
    {
        try {
            DB::beginTransaction ();
            $data = [
                'name' => $request->get('name'),
                'car_number' => $request->get('car_number'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $request->get('category_id'),
                'mobile_verified' => 1,
                'is_offline' => 1
            ];
            if ($request->driving_license) {
                $path  =  $this->upload($request->driving_license,'driving_licenses');
                $data['driving_license'] = $path;
            }
            if ($request->profile_img) {
                $avatar_url  =  $this->upload($request->profile_img,'profile_images');
                $data['avatar_url'] = $avatar_url;
            }
            if (isset($request->email)) {
                $data['email'] = $request->email;
            }
            if (isset($request->shop_id)) {
                $data['shop_id'] = $request->shop_id;
            }
            $deliveryBoy = $this->deliveryBoy->create($data);
            $accessToken = $this->deliveryBoy->createToken('authToken')->accessToken;
            DB::commit();
            $shop_type = $deliveryBoy->category->title;
            if(!$shop_type){
                $shop_type = '';
            }
            return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            DB::beginTransaction ();
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
            return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken]);
        } else {
            return response(['errors' => ['Password is not correct']], 402);
        }
            DB::commit();
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
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
