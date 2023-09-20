<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Shop;

class WalletCoupons extends Model
{
    use HasFactory;
    protected $guarded =[];
    
    public Function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public Function shop(){
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
