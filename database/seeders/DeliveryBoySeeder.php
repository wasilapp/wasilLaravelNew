<?php

namespace Database\Seeders;

use App\Models\DeliveryBoy;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DeliveryBoySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deliveryBoys = [
            [
                //'name' => "Delivery Boy 1",// Charles Jones
                'name' => [
                    'en' => 'Delivery Boy 1',
                    'ar' =>'سائق 1'
                ],
                'email' => "delivery.boy@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'driving_license' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'latitude' => 37.421104,
                'longitude' => -122.086951,
                'mobile'=>"+918469435337",
                "mobile_verified"=>true,
                "category_id"=>2,
                "agency_name" => 'وكالة غاز',
                "car_number"=>123,
                "distance" => 50,
                "is_approval"=>2,
                "shop_id"=>1

            ],
        ];

        foreach ($deliveryBoys as $deliveryBoy) {
            DeliveryBoy::create($deliveryBoy);
        }
    }
}
