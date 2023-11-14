<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\StatuSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            ManagerSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            ShopSeeder::class,
            CouponSeeder::class,
           // ShopCouponSeeder::class,
            DeliveryBoySeeder::class,
            UserAddressSeeder::class,
            StatuSeeder::class
        ]);
    }
}
