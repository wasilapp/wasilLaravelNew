<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Trait\MessageTrait;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ForgotPasswordController extends Controller
{
    use MessageTrait;

    public function sendResetLinkEmail(Request $request)
    {
        try {
            DB::beginTransaction();
             $request->validate([
                'mobile' => 'required',
                'password'=>'required'
            ]);
            $user =User::where('mobile','LIKE','%'.$request->mobile)->first();
            if(!$user){
                return $this->errorResponse(trans('message.mobile-not-found'),403);
            }
            if (Hash::check($request->get('password'), $user->password)) {
                return $this->errorResponse(trans('message.password-same'), 403);
            }
            $user->password = Hash::make($request->get('password'));
            if ($user->save()) {
                DB::commit();
                return $this->returnMessage(trans('message.password-reset-done'), 204);
            }
            DB::commit();
            return $this->returnMessage(trans('message.verify-mobile'),204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response(['errors' => [$e->getMessage()]], 402);
        }
    }
}
