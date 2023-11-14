<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomValidationException;
use App\Http\Controllers\FCMController;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\User\LoginUserRequest;
use App\Http\Requests\Api\User\RegisterUserRequest;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request as FacadesRequest;

class AuthController extends Controller
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

   public function register(Request $request)
   {

        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'mobile' => 'required|string|unique:users',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
                'account_type' => 'required',
                'referrer_link' => 'string'
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $existingUser = $this->user->where('mobile', $request->mobile)->first();
            if ($existingUser) {
                return $this->errorResponse([trans('message.mobile-already-register')], 400);
            }
            $message = '';

            $uniqueId = uniqid();
            $referrer = 'user_id=' . $uniqueId;
            $data = [
                'name' => $request->get('name'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'email' => $request->get('email'),
                'account_type' => $request->get('account_type'),
                'referrer' => $referrer,
            ];
            
            if ($request->has('referrer_link')) {
                $referrerLink = $request->referrer_link;
                $parts = explode('=', $referrerLink);
                $variableName = $parts[0];
                $idValue = $parts[1];
                if ($variableName === 'shop_id') {
                    $referrerShop = $this->manager->where('referrer', $request->referrer_link)->first();
                    $referrerShopId = $referrerShop->id;
                    $referrerShopName = $referrerShop->shop->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerShopId . trans('message.referral-name') . ' ' . $referrerShopName;
                    $data['referrer_link'] = $referrerLink;
                }elseif ($variableName === 'user_id') {
                    $referrerUser = $this->user->where('referrer', $request->referrer_link)->first();
                    $referrerUserId = $referrerUser->id;
                    $referrerUserName = $referrerUser->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerUserId . trans('message.referral-name') . ' ' . $referrerUserName;
                    $data['referrer_link'] = $referrerLink;
                }elseif ($variableName === 'driver_id') {
                    $referrerDeliveryBoy = $this->deliveryBoy->where('referrer', $request->referrer_link)->first();
                    $referrerDeliveryBoyId = $referrerDeliveryBoy->id;
                    $referrerDeliveryBoyName = $referrerDeliveryBoy->name;
                    $message = trans('message.referral-successfully') . ' ' . $referrerDeliveryBoyId . trans('message.referral-name') . ' ' . $referrerDeliveryBoyName;
                    $data['referrer_link'] = $referrerLink;
                }else {
                    return $this->errorResponse(trans('message.no-referral'), 400);
                }
            }

            if ($request->fcm_token) {
                $fcm_token = $request->fcm_token;
                $data['fcm_token'] = $fcm_token;
            }
            if ($request->avatar_url) {
                $path  =  $this->upload($request->avatar_url,'users/avatar_url');
                $data['avatar_url'] = $path;
            }
            $user = $this->user->create($data);
            $accessToken = $user->createToken('authToken')->accessToken;
            DB::commit();
            if (!empty($message)) {
                return $this->returnData('data', ['user' => $user, 'token' => $accessToken, 'referrer_message' => $message]);
            } else {
                return $this->returnData('data', ['user' => $user, 'token' => $accessToken]);
            }
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
                'mobile' =>'required',
                'password'=>'required'
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            $user = $this->user->where('mobile', $request->mobile)->first();

            if (!$user) {
                return $this->errorResponse(trans('message.mobile-not-found'), 403);
            }

            if(!Hash::check($request->password, $user->password)) {
                return $this->errorResponse(trans('message.password-correct'),400);
            }

            if ($user->blocked == 1) {
                return $this->errorResponse(trans('message.user-block'), 400);
            }
            $accessToken = $user->createToken('authToken')->accessToken;
            if(isset($request->fcm_token)){
                $user = $this->user->find($user->id);
                $user->fcm_token = $request->fcm_token;
                $user->save();
            }
            DB::commit();
            return $this->returnData('data', ['user'=>$user,'token'=>$accessToken]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
   }


   public function updateProfile(Request $request){
        try {
            DB::beginTransaction ();
            $user = auth()->user();
            $user = $this->user->where('id', $user->id)->first();
            if (!$user) {
                return $this->errorResponse(trans('User-not-found'),400);
            }
            $data = [];
            if ($request->has('name')) {
                $data['name'] = $request->name;
            }
            if ($request->has('mobile')) {
                $data['mobile'] = $request->mobile;
            }
            if ($request->has('email')) {
                $data['email'] = $request->email;
            }
            if ($request->has('fcm_token')) {
                $data['fcm_token'] = $request->fcm_token;
            }
            if ($request->has('password')) {
                $password = Hash::make($request->password);
                $data['password'] = $password;
            }
            if ($request->has('avatar_url')) {
                $avatar_url = $this->upload($request->avatar_url, 'users/avatar_url');
                $data['avatar_url'] = $avatar_url;
            }
            $user->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['user'=>$user],trans('message.account-modified'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
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

            if (User::where('mobile', $request->input('mobile'))->exists()) {
                DB::rollBack();
                return $this->errorResponse([trans('message.mobile-already-register')], 400);
            }
            DB::commit();
            return $this->returnMessage(trans('message.verify-mobile'),204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function mobileVerified(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
            }
            $user = $this->user->find(auth()->user()->id);
            $data = [
                "mobile" => $request->get('mobile'),
                "mobile_verified" => true
            ];

            if (!$user->mobile) {
                return $this->returnError('400', 'Invalid FCM token for the user.');
            }

            $user->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['user' => $user], trans('message.verification-code-sent'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
//    public function mobileVerified(Request $request)
//    {
//         try {
//             DB::beginTransaction();
//             $validator = Validator::make($request->all(),[
//                 'mobile'=>'required',
//             ]);

//             if ($validator->fails())
//             {
//                 return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
//             }
//             $user =  auth()->user();
//             $data = [
//                 "mobile" => $request->get('mobile'),
//                 "mobile_verified" => true
//             ];
//             $user->update($data);
//             DB::commit();
//             return $this->returnDataMessage('data', ['user'=>$user],trans('message.account-modified'));
//         } catch (\Exception $e) {
//             Log::error($e->getMessage());
//             DB::rollBack();
//             return $this->returnError('400', $e->getMessage());
//         }
//    }

   public function delete(Request $request)
   {
        try {
            DB::beginTransaction();

            $user = $this->user->find(auth()->user()->id);
            $orders = $user->orders;
            foreach ($orders as $order){
                if($order->status <= 5){
                    return $this->errorResponse(trans('message.You have active orders, please cancel all orders first'), 200);
                }
                $order->delete();
            }
            if($user->delete()){
                DB::commit();
                return $this->returnMessage(trans('message.Account-Deleted-Success'),204);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
   }

   public function parseReferralLink(Request $request)
    {
        $referrerLink = $request->input('referrer');

        $referrerUserId = substr($referrerLink, strpos($referrerLink, "user_id=") + 8);

        $referrerUser = User::where('referrer_link','=',$referrerLink)->first();

        if ($referrerUser) {
            $referrerUserName = $referrerUser->name;
            return $this->returnData('data', [trans('message.referral-successfully') . $referrerUserId .  trans('message.user-name') . $referrerUserName]);
        } else {
            return $this->errorResponse(trans('message.no-user-referral'), 400);
        }
    }

}

