<?php

namespace App\Notifications\Fairbase;

use App\Models\User;
use App\Models\Admin;
use App\Models\DeliveryBoy;
use App\Models\Manager;
use \Illuminate\Support\Str;

trait GeneralFromAdminNotificationFcm
{
    function SendGeneralFromAdminNotificationFcm($notificationData, $type, $to=null){
       
        $url = 'https://fcm.googleapis.com/fcm/send';
        if($type == "allManagers"){
            $FcmToken = Manager::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        }elseif($type == "allDeliveryBoys") {
            $FcmToken = DeliveryBoy::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        }elseif($type == "allUsers") {
            $FcmToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
           
        }elseif($type == "specific-manager"){
            $FcmToken = Manager::whereNotNull('fcm_token')->where('id', $to)->pluck('fcm_token')->all();
        }elseif($type == "specific-delivery-boy"){
            $FcmToken = DeliveryBoy::whereNotNull('fcm_token')->where('id', $to)->pluck('fcm_token')->all();
        }elseif($type == "specific-user"){
            $FcmToken = User::whereNotNull('fcm_token')->where('id', $to)->pluck('fcm_token')->all();
        }else{
            return;
        }
       // $FcmToken = Admin::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        $serverKey = 'AAAAiFoKgOc:APA91bHI-o35ce4wtOFjtg14sHrzQJbUMP5_jtRXsF_V9wzjHyYPxFE8zp8BIKvsW46MWSnhf7GEQCeEhWeRrLpPydG93jmQPp-G-CYm76uiYQrztMMYkNwU75sgyGzfQOFP007WRhnp'; // ADD SERVER KEY HERE PROVIDED BY FCM
   
       // dd($FcmToken);
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => $notificationData
        ];
     // dd($data);
        $encodedData = json_encode($data);
       // dd($encodedData);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
        // FCM response
    //dd($result);
    }
}
