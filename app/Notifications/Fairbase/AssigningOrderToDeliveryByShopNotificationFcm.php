<?php

namespace App\Notifications\Fairbase;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Helpers\AppSetting;
use \Illuminate\Support\Str;
use App\Models\DeliveryBoy;

trait AssigningOrderToDeliveryByShopNotificationFcm
{

    function sendAssigOrderToDeliveryByShopNotiFcm(Order $order, DeliveryBoy $deliveryBoy){
          // dd($deliveryBoy->fcm_token);
          $token = $deliveryBoy->fcm_token;
           $url = 'https://fcm.googleapis.com/fcm/send';

           $FcmToken =DeliveryBoy::where('fcm_token',$token)->pluck('fcm_token')->all();
         //  $order =$this->order->where('id',$order->id)->first();
        // dd($FcmToken);
           $serverKey = AppSetting::$FCM_SERVER_KEY;

           $user = auth()->user()->shop;

           if ($user->image_url) {
               $img =  asset( $user->image_url ) ;
           } else {
               $img ='assets/images/logo-light.png';
           }

           $data = [
               "registration_ids" => $FcmToken,
               "notification" => [
                   'en'=>[
                        'title'=>'new order',
                        'body'=>"A new order has been assigned to you by {$user->getTranslations('name')['en']} shop",
                   ],
                   'ar'=>[
                        'title'=>'طلب جديد',
                        'body'=>"تم اسناد طلب جديد إليك بواسطة متجر {$user->getTranslations('name')['ar']}",
                   ],
                   'icon'=>$img,
                   'data'=>$order,
                   'url'=>'',
               ]
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
