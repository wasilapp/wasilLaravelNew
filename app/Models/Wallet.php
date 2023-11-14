<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\WalletCoupons;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory,HasTranslations;

    public static $WALLET_WAIT_FOR_CONFIRMATION = 1;
    public static $WALLET_ACCEPTED_BY_ADMIN = 2;
    public static $WALLET_REJECTED_BY_ADMIN = 3;
    protected $fillable = [
        'title','description','statu','price','usage','shop_id','subcategory_id','active','image_url'
    ];
    public $translatable = ['title','description' ];
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class,'subcategory_id' ,'id');
    }

    public function walletCoupons()
    {
        return $this->hasMany(WalletCoupons::class);
    }
}
