<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletCoupons extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'wallet_id','usage','price','status','is_paid'
    ];

    public Function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public Function wallet(){
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
    public function walletCoupons()
    {
        return $this->hasMany(Order::class);
    }
}
