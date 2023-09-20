<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class  AuthController extends Controller
{
   public function register(Request  $request){
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'mobile' => 'required|unique:managers',
                'password' => 'required',
                // 'avatar_url' => 'required',
                'address' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        if(Manager::where('mobile','LIKE','%'.$request->mobile)->first()){
           return response(['message'=>"Number is  registered"], 200);

       }
        $Manager = new Manager();
        $Manager->name = $request->get('name');
        $Manager->mobile = $request->get('mobile');
        $Manager->password = Hash::make($request->get('password'));
        // $image = $request->file('avatar_url')->store('avatar_url', 'public');
        if (isset($request->fcm_token)) {
            $Manager->fcm_token = $request->fcm_token;
        }

        if($request->email){
            $this->validate($request,[
                'email' => 'required|email|unique:managers',
            ]);
            $Manager->email = $request->get('email');
        }

        // $Manager->avatar_url = $image;
        $Manager->mobile_verified = 1;
        $Manager->save();
        $accessToken = $Manager->createToken('authToken')->accessToken;

        $Manager_new = Manager::where('id',$Manager->id)->first();
        //  $shop_type = $Manager_new->category->title;
        // if(!$shop_type){
        //     $shop_type = '';
        // }
        return response(['Manager' => $Manager_new, 'token' => $accessToken]);
       //sleep(3);

    //    $validator = Validator::make($request->all(),[
    //       'name'=>'required',
    //       'email'=>'required|email|unique:managers',
    //       'password'=>'required'
    //    ]);

    //    if ($validator->fails())
    //    {
    //        return response(['errors'=>$validator->errors()->all()], 422);
    //    }

    //    $user  = new Manager();
    //    $user->name = $request->get('name');
    //    $user->email = $request->get('email');
    //    $user->password = Hash::make($request->get('password'));
    //    if(isset($request->fcm_token)){
    //        $user->fcm_token = $request->fcm_token;
    //    }
    //    $user->save();

    //    $accessToken = $user->createToken('authToken')->accessToken;
    //    return response(['user'=>$user,'token'=>$accessToken]);

   }

   public function login(Request $request){

       //sleep(3);

        $data = $request->validate([
           'mobile'=>'required',
           'password'=>'required'
       ]);


       if(!Manager::where('mobile', '=', $request->mobile)->exists()){
           return response(['errors'=>['This email is not found']],402);
       }

       $manager = Manager::where('mobile', '=', $request->mobile)->first();

        if ($manager && Hash::check($request->password, $manager->password)) {
            if(!$manager->mobile_verified){
                 return response(['errors' => [trans('admin.wait_verification')]], 402);
            }
            $accessToken = $manager->createToken('authToken')->accessToken;
            if (isset($request->fcm_token)) {
                $manager->fcm_token = $request->fcm_token;
            }
            $manager->save();
            return response(['manager' => $manager, 'token' => $accessToken], 200);
        } else {
            return response(['errors' => ['Password is not correct']], 402);
        }
   }


   public function updateProfile(Request $request){

//       return response(['errors' => ['This is demo version' ]], 403);
       $manager =  Manager::find(auth()->user()->id);

       if(isset($request->password)){
           $manager->password = Hash::make($request->password);
       }

       if(isset($request->avatar_image)){
           $url = "manager_avatars/".Str::random(10).".jpg";
           $oldImage = $manager->avatar_url;
           $data = base64_decode($request->avatar_image);
           Storage::disk('public')->put($url, $data);
           Storage::disk('public')->delete($oldImage);
           $manager->avatar_url = $url;
       }

       if($manager->save()){
           return response(['message'=>['Your setting has been changed'],'manager'=>$manager],200);
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

        if(Manager::where('mobile',$request->mobile)->exists()){
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
            return response(['message'=>['Your setting has been changed'],'manager'=>$user],200);
        }else{
            return response(['errors'=>['There is something wrong']],402);
        }
    }
}
