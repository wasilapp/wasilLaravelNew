<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\RegisterUserRequest;
use App\Http\Requests\Api\User\LoginUserRequest;
use App\Http\Trait\MessageTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    use MessageTrait;

   public function register(RegisterUserRequest $request)
   {
        try {
            DB::beginTransaction ();
            $existingUser = User::where('mobile', $request->mobile)->first();
            if ($existingUser) {
                return $this->errorResponse([trans('message.mobile-already-register')], 402);
            }
            $data = [
                'name' => $request->get('name'),
                'mobile' => $request->get('mobile'),
                'password' => Hash::make($request->get('password')),
                'email' => $request->get('email'),
                'mobile_verified' =>  $request->get('mobile_verified'),
            ];
            if ($request->fcm_token) {
                $fcm_token = $request->fcm_token;
                $data['fcm_token'] = $fcm_token;
            }
            if ($request->avatar_url) {
                $path  =  $this->upload($request->avatar_url,'avatar_url');
                $data['avatar_url'] = $path;
            }
            $user = User::create($data);
            $accessToken = $user->createToken('authToken')->accessToken;
            DB::commit();
            return $this->returnData('data', ['user'=>$user,'token'=>$accessToken]);
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
   }


   public function login(LoginUserRequest $request)
   {
        try {
            DB::beginTransaction ();
            $user = User::where('mobile', $request->mobile)->first();
            if(!User::where('mobile', $request->mobile)->exists()){
                return $this->errorResponse(trans('message.mobile-not-found'),402);
            }
            if(User::where('blocked',1)->exists()){
                return $this->errorResponse(trans('message.user-block'),402);
            }
            if(!Hash::check($request->password, $user->password)) {
                return $this->errorResponse(trans('message.password-correct'),402);
            }
            $accessToken = $user->createToken('authToken')->accessToken;
            if(isset($request->fcm_token)){
                $user = User::find($user->id);
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

       $user =  auth()->user();

       if(isset($request->password)){
           $user->password = Hash::make($request->password);
       }

       if(isset($request->avatar_image)){
           $url = "user_avatars/".Str::random(10).".jpg";
           $oldImage = $user->avatar_url;
           $data = base64_decode($request->avatar_image);
           Storage::disk('public')->put($url, $data);
           Storage::disk('public')->delete($oldImage);
           $user->avatar_url = $url;
       }

       if($user->save()){
           return response(['message'=>['Your setting has been changed'],'user'=>$user]);
       }else{
           return response(['errors'=>['There is something wrong']],402);
       }
   }

   public function verifyMobileNumber(Request $request){

       $validator = Validator::make($request->all(),[
           'mobile'=>'required',
       ]);

       if ($validator->fails())
       {
           return response(['errors'=>$validator->errors()->all()], 422);
       }

       if(User::where('mobile',$request->mobile)->exists()){
           return response(['errors'=>['Mobile number already exists']],402);

       }else{
           return response(['message'=>['You can verify with this mobile']]);
       }
   }

   public function mobileVerified(Request $request){

       $validator = Validator::make($request->all(),[
           'mobile'=>'required',
       ]);

       if ($validator->fails())
       {
           return response(['errors'=>$validator->errors()->all()], 422);
       }

       $user =  auth()->user();

       $user->mobile=$request->get('mobile');
       $user->mobile_verified=true;


       if($user->save()){
           return response(['message'=>['Your setting has been changed'],'user'=>$user],200);
       }else{
           return response(['errors'=>['There is something wrong']],402);
       }
   }

   public function delete(Request $request){

       $user =  auth()->user();

       if(isset($request->blocked)){
           $user->blocked = $request->blocked;
       }

       if($user->update()){
           return response(['message'=>['Your Account has been Deleted Successfully']]);
       }else{
           return response(['errors'=>['There is something wrong']],402);
       }
   }

}

