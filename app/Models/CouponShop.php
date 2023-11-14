<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
/**
 * @property mixed coupon_id
 * @property mixed shop_id
 * @method static create(int[] $deliveryCoupon)
 */
class CouponShop extends Pivot
{
    use HasFactory;
    protected $table = 'coupon_shop';
    protected $fillable = [
        'shop_id','coupon_id'
    ];

}
