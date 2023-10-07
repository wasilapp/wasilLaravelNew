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
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\User\LoginUserRequest;
use App\Http\Requests\Api\User\RegisterUserRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;

class AuthController extends Controller
{
    use MessageTrait;

    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

   public function register(Request $request)
   {

        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'mobile' => 'required|string|unique:users',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
                'account_type' => 'required'
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
            $uniqueId = uniqid(); // الحصول على معرِّف فريد
            $referrer = 'user_id=' . $uniqueId; // إنشاء رابط الاحالة
            $data = [
                'name' => $request->get('name'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'email' => $request->get('email'),
                'account_type' => $request->get('account_type'),
                'referrer' => $referrer,
            ];
            // return $data;
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
            return $this->returnData('data', ['user'=>$user,'token'=>$accessToken]);
        } catch (CustomValidationException $e) {
            // If a validation exception occurs, customize the validation response.
            $validationErrors = $e->validator->errors()->all();

            return response(['errors' => $validationErrors], 400);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
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
            return response(['errors' => [$e->getMessage()]], 402);
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

            if (User::where('mobile', $request->input('mobile'))->exists()) {
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

   public function delete(Request $request)
   {
        try {
            DB::beginTransaction();
            //$user =  auth()->user();
            $user = User::find(auth()->user()->id);
            if($user->delete()){
                DB::commit();
                return $this->returnMessage(trans('message.Account-Deleted-Success'),204);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }
   }

   public function parseReferralLink(Request $request)
    {
        $referrerLink = $request->input('referrer');

        $referrerUserId = substr($referrerLink, strpos($referrerLink, "user_id=") + 8);

        $referrerUser = User::where('referrer','=',$referrerLink)->first();;

        if ($referrerUser) {
            $referrerUserName = $referrerUser->name;
            return $this->returnData('data', [trans('message.referral-successfully') . $referrerUserId .  trans('message.user-name') . $referrerUserName]);
        } else {
            return $this->errorResponse(trans('message.no-user-referral'), 400);
        }
    }

}

