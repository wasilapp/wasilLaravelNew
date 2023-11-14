<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\AppSetting;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FCMController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function updateDeviceToken(Request $request)
    {
        $admin = Admin::find(Auth::guard('admin')->user()->id);
       //dd($admin);
         $admin->update([
            'fcm_token' => $request->token
        ]);
       // dd($admin);
        return response()->json(['Token successfully stored.']);
    
    }
    public function getLatestNotifications(Request $request)
    {
        if($user= Auth::user()){
            $user= Auth::user();
        } else {
            $user = Auth::guard('admin')->user();
        }
        

        $notifications =  $user->notifications()->latest()->limit(1)->get();
        $unreadNotifications =  $user->unreadNotifications()->count();

        return response()->json(['notifications' => $notifications , 'unreadNotifications' => $unreadNotifications ]);
    }
    
    public static function sendMessage($title,$body,$token,$screen="order",$data=null)
    {

        if(!$token)
            return false;

        $server_key = AppSetting::$FCM_SERVER_KEY;

        $json_data = [
            "to" => $token,
            "priority"=>"high",
            "notification" => [
                "title" => $title,
                "body" => $body,
                "click_action"=>"FLUTTER_NOTIFICATION_CLICK"
            ],
            "data" => [
                'screen'=> $screen
            ]
        ];
        $data = json_encode($json_data);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            curl_close($ch);
            return 'Oops! FCM Send Error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            return $result;
        }
    }

    public static function sendMessageToAll($title,$body): bool
    {

        $users = User::all();
        foreach ($users as $user) {
            self::sendMessage($title,$body,$user->fcm_token);
        }
        return true;
    }

    public static function test()
    {

        $token = User::find(1)->fcm_token;
        $title = "Test";
        $body= "Test";


        $server_key = AppSetting::$FCM_SERVER_KEY;

        $json_data = [
            "to" => $token,
            "priority"=>"high",
            "notification" => [
                "title" => $title,
                "body" => $body,
            ],
            /*  "data" => [
                  'key'=>'value'
              ]*/
        ];
        $data = json_encode($json_data);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            curl_close($ch);
            return 'Oops! FCM Send Error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            return $result;
        }
    }
}
