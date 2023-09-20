<?php

namespace Database\Seeders;

use App\Models\ShopCoupon;
use Illuminate\Database\Seeder;

class ShopCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shopCoupons = [
            [
                'shop_id' => 1,
                'coupon_id' => 1,
            ],
            [
                'shop_id' => 1,
                'coupon_id' => 2,
            ],
            [
                'shop_id' => 1,
                'coupon_id' => 3,
            ],
            [
                'shop_id' => 1,
                'coupon_id' => 4,
            ],

            [
                'shop_id' => 2,
                'coupon_id' => 1,
            ],
            [
                'shop_id' => 2,
                'coupon_id' => 2,
            ],
            [
                'shop_id' => 2,
                'coupon_id' => 3,
            ],
        ];
        
        foreach ($shopCoupons as $shopCoupon) {
            ShopCoupon::create($shopCoupon);
        }

    }
}
