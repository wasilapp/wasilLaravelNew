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
use App\Http\Requests\Api\DeliveryBoy\LoginDeliveryBoyRequest;
use App\Http\Requests\Api\DeliveryBoy\RegisterDeliveryBoyRequest;


class AuthController extends Controller
{
    use MessageTrait;
    use UploadImage;

    private $deliveryBoy;
    public function __construct(DeliveryBoy $deliveryBoy)
    {
        $this->deliveryBoy = $deliveryBoy;
    }
    public function register(Request $request)
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
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $data = [
                'name' => $request->get('name'),
                'car_number' => $request->get('car_number'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'category_id' => $request->get('category_id'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                // 'distance' => $request->get('distance'),
            ];
            if ($request->input('category_id') != 2) {
                $request->merge(['agency_name' => null]);
            }
            if (isset($request->distance)) {
                $data['distance'] = $request->distance;
            }
            if ($request->driving_license) {
                $path  =  $this->upload($request->driving_license,'deliveryBoys/driving_licenses');
                $data['driving_license'] = $path;
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
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
            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken], trans('message.account-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'mobile' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $deliveryBoy = $this->deliveryBoy->where('mobile', $request->mobile)->first();

            if(!$this->deliveryBoy->where('mobile', $request->mobile)->exists()){
                return $this->errorResponse(trans('message.mobile-not-found'),403);
            }

            if(!Hash::check($request->password, $deliveryBoy->password)) {
                return $this->errorResponse(trans('message.password-correct'),400);
            }

            if($deliveryBoy->is_verified == 0){
                return $this->errorResponse(trans('message.deliveryBoy-Inactive-by-manager'),403);
            }

            if($deliveryBoy->is_verified == 1){
                return $this->errorResponse(trans('message.deliveryBoy-Inactive-by-admin'),403);
            }


            $accessToken = $deliveryBoy->createToken('authToken')->accessToken;
            if(isset($request->fcm_token)){
                $deliveryBoy = $this->deliveryBoy->find($deliveryBoy->id);
                $deliveryBoy->fcm_token = $request->fcm_token;
                $deliveryBoy->save();
            }
            $shop_type = $deliveryBoy->category->title;
            if(!$shop_type){
                $shop_type = '';
            }
            DB::commit();
            return $this->returnData('data', ['deliveryBoy'=>$deliveryBoy,'token'=>$accessToken]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }

    public function changeStatus(Request $request)
    {
        try {
            DB::beginTransaction ();
            $this->validate($request, [
                'is_offline' => 'required'
            ]);
            $deliveryBoy = $this->deliveryBoy->find(auth()->user()->id);
            if ($request->is_offline) {
                if ($deliveryBoy->is_free) {
                    $deliveryBoy->is_offline = $request->is_offline;
                } else {
                    return response(['errors' => ['Please delivered current order then you can goes to offline']], 402);
                }
            } else {
                $deliveryBoy->is_offline = $request->is_offline;
            }
            DB::commit();

            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy],trans('message.Your status has been changed'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction ();
            $user = auth()->user();
            $deliveryBoy = $this->deliveryBoy->where('mobile', $user->mobile)->first();

            if (!$deliveryBoy) {
                return $this->errorResponse(trans('message.deliveryBoy-not-found'),400);
            }
            $data = [
                "is_approval" => 0
            ];
            if ($request->has('name')) {
                $data['name'] = $request->name;
            }
            if ($request->has('car_number')) {
                $data['car_number'] = $request->mobile;
            }
            if ($request->has('mobile')) {
                $data['mobile'] = $request->mobile;
            }
            if ($request->has('email')) {
                $data['email'] = $request->email;
            }
            if ($request->has('distance')) {
                $data['distance'] = $request->distance;
            }
            if ($request->has('password')) {
                $password = Hash::make($request->password);
                $data['password'] = $password;
            }
            if ($request->avatar_url) {
                $avatar_url  =  $this->upload($request->avatar_url,'deliveryBoys/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            if ($request->driving_licenses) {
                $driving_licenses  =  $this->upload($request->driving_licenses,'deliveryBoys/avatar_url');
                $data['driving_licenses'] = $driving_licenses;
            }
            $deliveryBoy->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['deliveryBoy'=>$deliveryBoy], trans('message.account-created-Please-wait-admin-approval'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }

    }

    public function verifyMobileNumber(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
            }

            if ($this->deliveryBoy->where('mobile', $request->input('mobile'))->exists()) {
                DB::rollBack();
                return $this->errorResponse([trans('message.mobile-already-register')], 400);
            }
            DB::commit();
            return $this->returnMessage(trans('message.verify-mobile'),204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }
    }

   public function mobileVerified(Request $request)
   {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'mobile'=>'required',
            ]);

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }
            $user =  auth()->user();
            $data = [
                "mobile" => $request->get('mobile'),
                "mobile_verified" => true
            ];
            $user->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['user'=>$user],trans('message.account-modified'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }
   }
}
