<?php

namespace App\Notifications\Fairbase;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Helpers\AppSetting;
use \Illuminate\Support\Str;
use App\Models\DeliveryBoy;

trait ChangeOrderStatuByShopNotificationFcm
{

    function sendChangeOrderStatuByShopNotificationFcm(Order $order){
          //dd($order->user->fcm_token)  ;
            $token = $order->user->fcm_token;
            $url = 'https://fcm.googleapis.com/fcm/send';
          //   $FcmToken = Admin::whereNotNull('fcm_token')->pluck('fcm_token')->all();
            $FcmToken =User::whereNotNull('fcm_token')->where('fcm_token',$token)->pluck('fcm_token')->all();
            //  $order =$this->order->where('id',$order->id)->first();

           $serverKey = AppSetting::$FCM_SERVER_KEY;

           $shop = auth()->user()->shop;

           if ($shop->image_url) {
               $img =  asset( $shop->image_url ) ;
           } else {
               $img ='assets/images/logo-light.png';
           }

           $data = [
               "registration_ids" => $FcmToken,
               "notification" => [
                    "title" => 'rahaf',
                    "body" => 'shop',
                   'en'=>[
                        'title'=>'order',
                        'body'=>"order has been accepted by {$shop->getTranslations('name')['en']} shop",
                   ],
                   'ar'=>[
                        'title'=>'طلب جديد',
                        'body'=>"تم قبول طلبك  بواسطة متجر {$shop->getTranslations('name')['ar']}",
                   ],
                   'icon'=>$img,
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
