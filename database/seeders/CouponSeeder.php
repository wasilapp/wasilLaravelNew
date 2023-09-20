<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coupons = [
            [
                'code' => 'SAVE40',
                'description' => '40% off at any products with product price above $300 and get upto $800 discount',
                'offer' => 40,
                'min_order' => 300,
                'max_discount' => 800,
                'for_new_user' => true,
                'for_only_one_time' => true,
                'expired_at' => now()->addDays(2),
            ],
            [
                'code' => 'GRUB10',
                'description' => 'Buy Product with above $50 and get 10% discount upto $200',
                'offer' => 10,
                'min_order' => 50,
                'max_discount' => 200,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'FLAT25',
                'description' => 'Flat 25% off on any Order with total amount greater than $100',
                'offer' => 25,
                'min_order' => 100,
                'max_discount' => 800,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'GET30',
                'description' => '30% off on any Order above $500 and win discount upto $300',
                'offer' => 30,
                'min_order' => 500,
                'max_discount' => 300,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'SALE50',
                'description' => '50% off at any Order above $800. Buy using code SALE50 and get upto $500 discount',
                'offer' => 50,
                'min_order' => 800,
                'max_discount' => 500,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'GET20',
                'description' => 'upto 20% off at any Order above $200',
                'offer' => 20,
                'min_order' => 200,
                'max_discount' => 200,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'SAVE10',
                'description' => '10% off with toal amount $50 and above on any Prduct',
                'offer' => 10,
                'min_order' => 50,
                'max_discount' => 100,
                'expired_at' => now()->addDays(2)
            ],
            [
                'code' => 'FLAT15',
                'description' => 'Get Flat 15% off on your Order $50 and above upto $100 discount',
                'offer' => 15,
                'min_order' => 50,
                'max_discount' => 100,
                'expired_at' => now()->addDays(2)
            ],
        ];
        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
