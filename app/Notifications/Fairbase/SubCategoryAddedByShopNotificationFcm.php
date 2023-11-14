<?php

namespace App\Notifications\Fairbase;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Helpers\AppSetting;
use \Illuminate\Support\Str;
use App\Models\Advertisement;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;

trait SubCategoryAddedByShopNotificationFcm
{
    
    
    function sendSubCategoryAddedByShopNotificationFcm(SubCategory $subcategory){
       // dd($subcategory);

        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = Admin::whereNotNull('fcm_token')->pluck('fcm_token')->all();
          
        // $serverKey = 'AAAAiFoKgOc:APA91bHI-o35ce4wtOFjtg14sHrzQJbUMP5_jtRXsF_V9wzjHyYPxFE8zp8BIKvsW46MWSnhf7GEQCeEhWeRrLpPydG93jmQPp-G-CYm76uiYQrztMMYkNwU75sgyGzfQOFP007WRhnp'; // ADD SERVER KEY HERE PROVIDED BY FCM
        // dd(  $serverKey);
        $serverKey = AppSetting::$FCM_SERVER_KEY;

        $user = auth()->user()->shop;
        $userName = $user->name;
        if ($user->image_url) {
            $img =  asset( $user->image_url ) ;
        } else {
            $img ='assets/images/logo-light.png';
        }

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                'en'=>[
                    'title'=>'Add a new item',
                    'body'=>"A new item  {$subcategory->getTranslations('title')['en']} has been added by {$userName}.",
                ],
                'ar'=>[
                    'title'=>'إضافة ايتم جديد',
                    'body'=>"{$userName}تم إضافة ايتم جديد  {$subcategory->getTranslations('title')['ar']} من قبل ",
                ],
                'icon'=>$img,
                'url'=>url("/admin/sub_categories/sub-categories-requests"),
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
    //  dd($result);
    }
}
