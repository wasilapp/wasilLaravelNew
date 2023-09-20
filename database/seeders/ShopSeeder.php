<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shops = [
            [
                //'name' => "Fashion Factory",// Fashion Factory
                'name' => [
                    'en' => 'Fashion Factory',
                    'ar' =>'مصنع الأزياء'
                ],
                'email' => "shop@demo.com",
                'mobile' => "789654123",
                'latitude' => 37.4235492,
                'longitude' => -122.0924447,
                'address' => "Garcia Ave, Mountain View",
                'image_url' => 'uploads/shops/1.jpg',
                'default_tax' => 10,
                'barcode' => '145234',
                'available_for_delivery' => true,
                'open' => true,
                'manager_id' => 1,
                'category_id' => 1,
                "delivery_range" => 99999999
            ],
            [
                //'name' => "The Corner Store",// The Corner Store
                'name' => [
                    'en' => 'The Corner Store',
                    'ar' =>'متجر الزاوية'
                ],
                'email' => "shop2@demo.com",
                'mobile' => "147852369",
                'latitude' => 37.4258241,
                'longitude' => -122.0810562,
                'address' => "Bill Graham Pkwy, Mountain View",
                'image_url' => 'uploads/shops/2.jpg',
                'default_tax' => 15,
                'barcode' => '145734',
                 'available_for_delivery' => true,
                'open' => true,
                'manager_id' => 2,
                'category_id' => 2,
                "delivery_range" => 99999999
            ],

        ];

        foreach ($shops as $shop) {
            Shop::create($shop);
        }
    }
}
