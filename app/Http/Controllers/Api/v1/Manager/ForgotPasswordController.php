<?php

namespace App\Http\Controllers\Api\v1\Manager;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use App\Models\Manager;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordController extends Controller
{
    use MessageTrait;

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email'
        ]);

        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->all(), 422);
        }
        if(!Manager::where('email','=',$request->email)->exists()){
            return $this->errorResponse(trans('message.email-is-not-registered'),403);

        }

        $response = Password::broker('managers')->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->returnMessage(trans('message.Your-password-reset-link-sent'),204);
        } else {
            return $this->errorResponse(trans('message.Email-already-sent-please-wait'),403);
        }
    }
}
