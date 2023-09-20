<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\DeliveryBoy;

use Illuminate\Support\Facades\Hash;


class ForgotPasswordController extends Controller
{

    public function sendResetLinkEmail(Request $request)
    {

       $request->validate([
            'mobile' => 'required',
            'password'=>'required'
        ]);

        $user =DeliveryBoy::where('mobile','LIKE','%'.$request->mobile)->first();
          if(!$user){
              return response(['message'=>"Number is  not exists"], 403);
          }

       $user->password = Hash::make($request->get('password'));
       

        if ($user->save()) {
            return response(['message' => 'Your password reset done.'], 200);
        } else {
            return response(['errors' => ['Something wrong']], 403);
    //     }
    }
        
    }


}
