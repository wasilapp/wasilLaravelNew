<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FCMController;
use App\Http\Requests\Api\Manager\LoginManagerRequest;
use App\Http\Requests\Api\Manager\RegisterManagerRequest;
use App\Http\Requests\ShopRequest;
use App\Http\Trait\MessageTrait;
use App\Http\Trait\UploadImage;
use App\Models\Admin;
use App\Models\Category;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\Shop;
use App\Models\ShopRevenue;
use App\Models\ShopReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Twilio\Rest\Client;


class  AuthController extends Controller
{
    use UploadImage;
    use MessageTrait;

    private $shop;
    private $manager;
    private $shopRevenue;
    private $category;
    private $shopReview;
    private $deliveryBoy;
    private $user;

    public function __construct(Shop $shop, Manager $manager,DeliveryBoy $deliveryBoy,User $user,ShopRevenue $shopRevenue,Category $category,ShopReview $shopReview)
    {
        $this->shop = $shop;
        $this->manager = $manager;
        $this->shopRevenue = $shopRevenue;
        $this->category = $category;
        $this->shopReview = $shopReview;
        $this->deliveryBoy = $deliveryBoy;
        $this->user = $user;
    }

   public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'shop.name.en' => 'required|unique:shops,name->en',
                'shop.name.ar' => 'required|unique:shops,name->ar',
                'distance' => 'integer|min:10',
                'email' => 'required|unique:managers,email',
                'mobile' => 'required|unique:managers,mobile',
                'shop.category' => 'required',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'manager.avatar_url' => 'required',
                'referrer_link' => 'string'
                // 'delivery_range' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            if($request->manager){
                $message = '';
                $uniqueId = uniqid();
                $referrer = 'shop_id=' .  $uniqueId;
                if ($request->manager['avatar_url']) {
                    $avatar_url_path  =  $this->upload($request->manager['avatar_url'],'managers/avatar_url');
                }
                if ($request->manager['license']) {
                $license_path  =  $this->upload($request->manager['license'],'managers/license');
                }
                $manger_data = [
                    'name' => [
                        'en' => $request->manager['name']['en'],
                        'ar' => $request->manager['name']['ar']
                    ],
                    'avatar_url' => $avatar_url_path,
                    'license' => $license_path,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'mobile' => $request->mobile,
                    'referrer' => $referrer,
                ];
                if ($request->fcm_token) {
                    $fcm_token = $request->fcm_token;
                    $manger_data['fcm_token'] = $fcm_token;
                }
                $manager = $this->manager->create($manger_data);
                $accessToken = $this->manager->createToken('authToken')->accessToken;
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
                        $manger_data['referrer_link'] = $referrerLink;
                    }elseif ($variableName === 'user_id') {
                        $referrerUser = $this->user->where('referrer', $request->referrer_link)->first();
                        $referrerUserId = $referrerUser->id;
                        $referrerUserName = $referrerUser->name;
                        $message = trans('message.referral-successfully') . ' ' . $referrerUserId . trans('message.referral-name') . ' ' . $referrerUserName;
                        $manger_data['referrer_link'] = $referrerLink;
                    }elseif ($variableName === 'driver_id') {
                        $referrerDeliveryBoy = $this->deliveryBoy->where('referrer', $request->referrer_link)->first();
                        $referrerDeliveryBoyId = $referrerDeliveryBoy->id;
                        $referrerDeliveryBoyName = $referrerDeliveryBoy->name;
                        $message = trans('message.referral-successfully') . ' ' . $referrerDeliveryBoyId . trans('message.referral-name') . ' ' . $referrerDeliveryBoyName;
                        $manger_data['referrer_link'] = $referrerLink;
                    }else {
                        return $this->errorResponse(trans('message.no-referral'), 400);
                    }
                }
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
                // 'delivery_range' => $request->delivery_range,
                // 'distance' => $request->distance,
                'manager_id' => $manager->id
            ];

            $number = $this->generateBarcodeNumber();

            if (isset($request->distance)) {
                $data['distance'] = $request->distance;
            }

            if (isset($request->delivery_range)) {
                $data['delivery_range'] = $request->delivery_range;
            }

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

            $shop = $this->shop->create($data);
            DB::commit();
            $managerData = [
                'id' => $manager->id,
                'name_en' => $manager->getTranslation('name', 'en'),
                'name_ar' => $manager->getTranslation('name', 'ar'),
                'email' => $manager->email,
                'mobile' => $manager->mobile,
                'mobile_verified' => $manager->mobile_verified,
                'avatar_url' => $manager->avatar_url,
                'license' => $manager->license,
                'is_approval' => $manager->is_approval,
                'referrer' => $manager->referrer,
                'referrer_link' => $manager->referrer_link,
                'fcm_token' => $manager->fcm_token,
                'shop_name_en' => $manager->shop->getTranslation('name', 'en'),
                'shop_name_ar' => $manager->shop->getTranslation('name', 'ar'),
                'barcode' => $manager->shop->barcode,
                'latitude' => $manager->shop->latitude,
                'longitude' => $manager->shop->longitude,
                'address' => $manager->shop->address,
                'rating' => $manager->shop->rating,
                'delivery_range' => $manager->shop->delivery_range,
                'total_rating' => $manager->shop->total_rating,
                'default_tax' => $manager->shop->default_tax,
                'available_for_delivery' => $manager->shop->available_for_delivery,
                'open' => $manager->shop->open,
                'category_id' => $manager->shop->category_id,
                'distance' => $manager->shop->distance,
                'created_at' => $manager->shop->created_at,
                'updated_at' => $manager->shop->updated_at,
            ];
            $fcm_token = Admin::whereNotNull('fcm_token')->pluck('fcm_token')->all();
            FCMController::sendMessage(trans('admin.shop_request'), trans('admin.New Shop needs acception'), $fcm_token);
            if (!empty($message)) {
                return $this->returnDataMessage('data', ['shop'=>$managerData,'token'=>$accessToken, 'referrer_message' => $message], trans('message.account-created-Please-wait-admin-approval'));
            } else {
                return $this->returnDataMessage('data', ['shop'=>$managerData,'token'=>$accessToken], trans('message.account-created-Please-wait-admin-approval'));
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
                'mobile' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
           DB::beginTransaction ();
            $manager = $this->manager->with('shop')->where('mobile', $request->mobile)->first();
           if(!$manager){
               return $this->errorResponse(trans('message.mobile-not-found'),403);
           }
           if(!Hash::check($request->password, $manager->password)) {
               return $this->errorResponse(trans('message.password-correct'),400);
           }
           if($manager->is_approval != 1){
                return $this->errorResponse(trans('admin.wait_verification'), 402);
            }
           $accessToken = $manager->createToken('authToken')->accessToken;
           if(isset($request->fcm_token)){
               $manager->fcm_token = $request->fcm_token;
           }
           $manager->save();
            $managerData = [
                'id' => $manager->id,
                'name_en' => $manager->getTranslation('name', 'en'),
                'name_ar' => $manager->getTranslation('name', 'ar'),
                'email' => $manager->email,
                'mobile' => $manager->mobile,
                'mobile_verified' => $manager->mobile_verified,
                'avatar_url' => $manager->avatar_url,
                'license' => $manager->license,
                'is_approval' => $manager->is_approval,
                'referrer' => $manager->referrer,
                'referrer_link' => $manager->referrer_link,
                'fcm_token' => $manager->fcm_token,
                'shop_name_en' => $manager->shop->getTranslation('name', 'en'),
                'shop_name_ar' => $manager->shop->getTranslation('name', 'ar'),
                'barcode' => $manager->shop->barcode,
                'latitude' => $manager->shop->latitude,
                'longitude' => $manager->shop->longitude,
                'address' => $manager->shop->address,
                'rating' => $manager->shop->rating,
                'delivery_range' => $manager->shop->delivery_range,
                'total_rating' => $manager->shop->total_rating,
                'default_tax' => $manager->shop->default_tax,
                'available_for_delivery' => $manager->shop->available_for_delivery,
                'open' => $manager->shop->open,
                'distance' => $manager->shop->distance,
                'category_id' => $manager->shop->category_id,
                'distance' => $manager->shop->distance,
                'created_at' => $manager->shop->created_at,
                'updated_at' => $manager->shop->updated_at,
            ];
           DB::commit();
           return $this->returnData('data', ['manager'=>$managerData,'token'=>$accessToken]);
       }catch(\Exception $e){
           Log::info($e->getMessage());
           DB::rollBack();
           return $this->returnError('400', $e->getMessage());
       }

   }

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction();
            $manager = auth()->user();
            $shop = auth()->user()->shop;
            if (!$manager || !$shop) {
                return $this->errorResponse(trans('manager-or-shop-not-found'), 400);
            }

            $managerData = $request->input('manager', []);
            $shopData = $request->input('shop', []);

            if (!empty($managerData)) {
                if (isset($managerData['avatar_url'])) {
                    $avatar_url_path = $this->upload($managerData['avatar_url'], 'managers/avatar_url');
                    $manager->avatar_url = $avatar_url_path;
                }

                if (isset($managerData['license'])) {
                    $license_path = $this->upload($managerData['license'], 'managers/license');
                    $manager->license = $license_path;
                }

                if (isset($managerData['name'])) {
                    $manager->name = [
                        'en' => $managerData['name']['en'],
                        'ar' => $managerData['name']['ar']
                    ];
                }
                if (isset($managerData['email'])) {
                    $manager->email = $managerData['email'];
                }
                if (isset($managerData['mobile'])) {
                    $manager->mobile = $managerData['mobile'];
                }
                if (isset($managerData['password'])) {
                    $manager->password = Hash::make($managerData['password']);
                }

                $manager->save();
            }

            if (!empty($shopData)) {
                if (isset($shopData['name'])) {
                    $shop->name = [
                        'en' => $shopData['name']['en'],
                        'ar' => $shopData['name']['ar']
                    ];
                }
                if (isset($shopData['address'])) {
                    $shop->address = $shopData['address'];
                }
                if (isset($shopData['latitude'])) {
                    $shop->latitude = $shopData['latitude'];
                }
                if (isset($shopData['longitude'])) {
                    $shop->longitude = $shopData['longitude'];
                }
                if (isset($shopData['default_tax'])) {
                    $shop->default_tax = $shopData['default_tax'];
                }
                if (isset($shopData['delivery_range'])) {
                    $shop->delivery_range = $shopData['delivery_range'];
                }
                if (isset($shopData['open'])) {
                    $shop->open = $shopData['open'];
                }
                if ($shopData['distance']) {
                    $shop->distance = $shopData['distance'];
                }
               
                $shop->save();
            }
            DB::commit();
            $managerData = [
                'id' => $manager->id,
                'name_en' => $manager->getTranslation('name', 'en'),
                'name_ar' => $manager->getTranslation('name', 'ar'),
                'email' => $manager->email,
                'mobile' => $manager->mobile,
                'mobile_verified' => $manager->mobile_verified,
                'avatar_url' => $manager->avatar_url,
                'license' => $manager->license,
                'is_approval' => $manager->is_approval,
                'referrer' => $manager->referrer,
                'referrer_link' => $manager->referrer_link,
                'shop_name_en' => $manager->shop->getTranslation('name', 'en'),
                'shop_name_ar' => $manager->shop->getTranslation('name', 'ar'),
                'barcode' => $manager->shop->barcode,
                'latitude' => $manager->shop->latitude,
                'longitude' => $manager->shop->longitude,
                'address' => $manager->shop->address,
                'rating' => $manager->shop->rating,
                'delivery_range' => $manager->shop->delivery_range,
                'total_rating' => $manager->shop->total_rating,
                'default_tax' => $manager->shop->default_tax,
                'available_for_delivery' => $manager->shop->available_for_delivery,
                'open' => $manager->shop->open,
                'category_id' => $manager->shop->category_id,
                'distance' => $manager->shop->distance,
                'created_at' => $manager->shop->created_at,
                'updated_at' => $manager->shop->updated_at,
            ];
            return $this->returnDataMessage('data', ['shop'=> $managerData], trans('message.account-modified'));
        } catch (\Exception $e) {
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

            if ($this->manager->where('mobile', $request->input('mobile'))->exists()) {
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

            $manager = $this->manager->find(auth()->user()->id);

            $data = [
                "mobile" => $request->get('mobile'),
                "mobile_verified" => true
            ];

            if (!$manager->mobile) {
                return $this->returnError('400', 'Invalid FCM token for the user.');
            }


            $manager->update($data);

            DB::commit();
            return $this->returnDataMessage('data', ['manager'=>$manager],trans('message.account-modified'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
    // public function mobileVerified(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $validator = Validator::make($request->all(), [
    //             'mobile' => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
    //         }

    //         $manager = auth()->user();
    //         $otp = rand(100000, 999999);
    //         $data = [
    //             "mobile" => $request->get('mobile'),
    //             "otp" => $otp,
    //             "otp_expiration" => Carbon::now()->addMinutes(15)
    //         ];

    //         $FCMController = FCMController::sendMessage("Your verification code is:", $data['otp'], $data['mobile']);
    //         return $FCMController;

    //         $manager->update($data);
    //         DB::commit();

    //         return $this->returnDataMessage('data', ['manager' => $manager], trans('message.verification-code-sent'));

    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage());
    //         DB::rollBack();
    //         return $this->returnError('400', $e->getMessage());
    //     }
    // }

    public function verifyOtp(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'otp' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
            }

            $manager = auth()->user();
            $storedOtp = $manager->otp;
            $otpExpiration = $manager->otp_expiration;
            if ($request->otp == $storedOtp ) {
                DB::commit();
                return $this->returnDataMessage('data', ['manager' => $manager], trans('message.verification-successfull'));
            } else {
                DB::commit();
                return $this->returnError( trans('message.sorry_code_worng'),402);
            }
            if($otpExpiration > Carbon::now()){
                DB::commit();
                return $this->returnError( trans('message.sorry_code_expired'),402);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function resendOtp(Request $request){
        try {
            DB::beginTransaction();
            $manager = auth()->user();
            $newOtp = rand(100000, 999999);
            $data = [
                "otp" => $newOtp,
                "otp_expiration" => Carbon::now()->addMinutes(15)
            ];

            $twilioAccountSid = env('TWILIO_ACCOUNT_SID');
            $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
            $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');
            $twilio = new Client($twilioAccountSid, $twilioAuthToken);

            $message = $twilio->messages->create(
                $manager['mobile'],
                [
                    'from' => $twilioPhoneNumber,
                    'body' => "Your verification code is: " . $data['otp']
                ]
            );
            $manager->update($data);
            DB::commit();
            return $this->returnMessage(trans('message.code_sent_again_success'),204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    public function statusShopOpenOrClose()
    {
        try {
            DB::beginTransaction();
            $manager = auth()->user();
            $shop = $manager->shop;

            if ($shop->open == 1) {
                $shop->open = 0;
                $message = trans('message.shop-closed');
            } else {
                $shop->open = 1;
                $message = trans('message.shop-opened');
            }
            $shop->save();
            DB::commit();
            return $this->returnMessage($message, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }

    // public function otpMobile(Request $request){
    //     try {
    //         DB::beginTransaction();
    //         $validator = Validator::make($request->all(),[
    //             'mobile'=>'required',
    //         ]);

    //         if ($validator->fails())
    //         {
    //             return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
    //         }
    //         $manager =  auth()->user();
    //         $otp = rand(100000, 999999);
    //         $data = [
    //             "mobile" => $request->get('mobile'),
    //             "otp" => $otp
    //         ];
    //         // return $data;
    //         $manager->update($data);
    //         DB::commit();
    //         FCMController::sendMessage(trans('messages.otp-message'), trans('messages.verification-code-sent') . ': ' . $otp, trans('messages.for-your-account'), auth()->user());
    //         return $this->returnDataMessage('data', ['manager' => $manager], trans('messages.verification-code-sent'));
    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage());
    //         DB::rollBack();
    //         return $this->returnError('400', $e->getMessage());
    //     }
    // }

    // public function verifyOtp(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'otp' => 'required|digits:6',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse(['errors' => $validator->errors()->all()], 422);
    //         }

    //         $manager = auth()->user();
    //         $inputOtp = $request->input('otp');

    //         if ( $manager->otp == $inputOtp) {
    //             return $this->returnDataMessage('data', ['manager' => $manager], trans('message.otp-verified-success'));
    //         } else {
    //             return $this->returnError('401', trans('messages.invalid-otp'));
    //         }
    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage());
    //         return $this->returnError('400', $e->getMessage());
    //     }
    // }

    function generateBarcodeNumber() {
        $number = mt_rand(100000, 999999);

        if ($this->shop->where('barcode',$number)->exists()) {
            return generateBarcodeNumber();
        }
        return $number;
    }

    public function delete(Request $request)
    {
            try {
                DB::beginTransaction();

                $manager = $this->manager->find(auth()->user()->id);
                $shop = $manager->shop;
                $orders =  $shop->orders;
                foreach ($orders as $order){
                    if($order->status >= 3){
                        return $this->errorResponse(trans('message.You have active orders, please cancel all orders first'), 200);
                    }
                    $order->delete();
                }
                $shop->delete();
                $manager->delete();
                DB::commit();
                return $this->returnMessage(trans('message.Account-Deleted-Success'),204);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                DB::rollBack();
                return $this->returnError('400', $e->getMessage());
            }
    }

}
