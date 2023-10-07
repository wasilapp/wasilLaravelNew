<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use App\Notifications\DeliveryBoyResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Translatable\HasTranslations;

/**
 * @method static where(string $string, string $string1, $string2)
 * @method static find($delivery_boy_id)
 * @method static doesnthave(string $string)
 * @method static has(string $string)
 */
class DeliveryBoy extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory,HasTranslations;

    // is_approval
    static $PenddingApproval = 0;
    static $ManagerApproval = 1;
    static $ManagerRefused = -1;
    static $AdminApproval = 2;
    static $AdminRefused = -2;

    protected $fillable = [
        'name','email','email_verified_at','password','fcm_token','latitude',
        'longitude','is_free','is_offline','is_approval','avatar_url','mobile',
        'mobile_verified','rating','total_rating', 'category_id', 'shop_id',
        'car_number','driving_license','is_verified','distance','agency_name'
    ];
    public $translatable = ['name','agency_name'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new DeliveryBoyResetPasswordNotification($token));
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
        public static function orders_total($id){
         $shop=DeliveryBoy::find($id);
        return $shop->orders->where('shop_id',Null)->sum('total');
    }
        public function transactions(){
        return $this->hasMany(Transaction::class);
    }
        public static function total_shop_to_admin($id){
        $shop=DeliveryBoy::find($id);
        return $shop->transactions->sum('total');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function delivery_type()
    {
        return $this->belongsTo(Shop::class);
    }
    public function ordersAssignToDelivery()
    {
        return $this->hasMany(AssignToDelivery::class);
    }

    public function calculateDistance($userLatitude, $userLongitude) {
       $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر
        $latDiff = deg2rad($userLatitude - $this->latitude);
        $lonDiff = deg2rad($userLongitude - $this->longitude);
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos(deg2rad($this->latitude)) * cos(deg2rad($userLatitude)) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        $this->distance = $distance;
        $this->save();

        return $distance;
    }
}
