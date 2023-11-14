<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
/**
 * @property mixed coupon_id
 * @property mixed delivery_boy_id
 * @method static create(int[] $deliveryCoupon)
 */
class CouponDeliveryBoy extends Pivot
{
    use HasFactory;
    protected $table = 'coupon_delivery_boy';
    protected $fillable = [
        'delivery_boy_id','coupon_id'
    ];

}
