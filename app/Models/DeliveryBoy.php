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

    protected $fillable = [
        'name','email','email_verified_at','password','fcm_token','latitude','longitude','is_free','is_offline','avatar_url','mobile','mobile_verified','rating','total_rating', 'category_id', 'shop_id','car_number','driving_license','is_verified'
    ];
    public $translatable = ['name'];

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
}
