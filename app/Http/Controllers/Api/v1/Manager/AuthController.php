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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class  AuthController extends Controller
{
    use UploadImage;
    use MessageTrait;

    private $shop;
    private $manager;
    private $shopRevenue;
    private $category;
    private $shopReview;

    public function __construct(Shop $shop, Manager $manager,ShopRevenue $shopRevenue,Category $category,ShopReview $shopReview)
    {
        $this->shop = $shop;
        $this->manager = $manager;
        $this->shopRevenue = $shopRevenue;
        $this->category = $category;
        $this->shopReview = $shopReview;
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
                // 'delivery_range' => 'required',
            ]);

            if ($validator->fails())
            {
                return $this->errorResponse($validator->errors()->all(), 422);
            }
            DB::beginTransaction ();
            if($request->manager){
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
                ];

                $manager = $this->manager->create($manger_data);
                $accessToken = $this->manager->createToken('authToken')->accessToken;
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
            $fcm_token = Admin::whereNotNull('fcm_token')->pluck('fcm_token')->all();
            FCMController::sendMessage(trans('admin.shop_request'), trans('admin.New Shop needs acception'), $fcm_token);
            return $this->returnDataMessage('data', ['manager'=>$manager,'shop'=>$shop,'token'=>$accessToken], trans('message.account-created-Please-wait-admin-approval'));
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
           $manager = $this->manager->where('mobile', $request->mobile)->first();

           if(!$manager){
               return $this->errorResponse(trans('message.mobile-not-found'),403);
           }
           if(!Hash::check($request->password, $manager->password)) {
               return $this->errorResponse(trans('message.password-correct'),400);
           }
           if(!$manager->is_approval){
                return response(['errors' => [trans('admin.wait_verification')]], 402);
            }
           $accessToken = $manager->createToken('authToken')->accessToken;
           if(isset($request->fcm_token)){
               $manager->fcm_token = $request->fcm_token;
           }
           $manager->save();
           DB::commit();
           return $this->returnData('data', ['manager'=>$manager,'token'=>$accessToken]);
       }catch(\Exception $e){
           Log::info($e->getMessage());
           DB::rollBack();
           return response(['errors' => [$e->getMessage()]], 402);
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
                if ($request->has('distance')) {
                    $data['distance'] = $request->distance;
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

            if ($this->manager->where('mobile', $request->input('mobile'))->exists()) {
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

    public function mobileVerified(Request $request){

        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'mobile'=>'required',
            ]);

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }
            $manager =  auth()->user();
            $data = [
                "mobile" => $request->get('mobile'),
                "mobile_verified" => true
            ];
            $manager->update($data);
            DB::commit();
            return $this->returnDataMessage('data', ['manager'=>$manager],trans('message.account-modified'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
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
            return response(['errors' => [$e->getMessage()]], 402);
        }
    }

    function generateBarcodeNumber() {
        $number = mt_rand(100000, 999999);

        if ($this->shop->where('barcode',$number)->exists()) {
            return generateBarcodeNumber();
        }
        return $number;
    }


}
