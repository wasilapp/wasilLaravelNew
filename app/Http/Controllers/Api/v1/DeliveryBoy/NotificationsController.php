<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use Illuminate\Http\Request;
use App\Http\Trait\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    use MessageTrait;
    
    public function index()
    {

        try {
            $delivery = Auth()->user();
           // return $delivery;
            //dd($user->notifications()->limit(10)->get());
            if($delivery){
                $notifications = $delivery->notifications()->get();
                $newCount = $delivery->unreadNotifications()->count();
            }
                        
            
            return $this->returnData('data', ['notifications'=>$notifications]);
            

        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            return $this->returnError('400', $e->getMessage());
        }
    }
}
