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
                "distance" => 50

            ],
            [
                'name' => [
                    'en' => 'Delivery Boy 2',
                    'ar' =>'سائق 2'
                ],
                'email' => "delivery.boy2@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'driving_license' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'mobile'=>"+918469435336",
                "mobile_verified"=>true,
                'latitude' => 37.419010,
                'longitude' => -122.077957,
                "category_id"=>1,
                "agency_name" => '',
                "car_number"=>1234,
                "distance" => 30
            ],
            [
                'name' => [
                    'en' => 'Delivery Boy 3',
                    'ar' =>'سائق 3'
                ],
                'email' => "delivery.boy3@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'driving_license' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'mobile'=>"+918469435335",
                "mobile_verified"=>true,
                'latitude' => 37.416797,
                'longitude' => -122.082967,
                "category_id"=>2,
                "agency_name" => 'وكالة غاز',
                "car_number"=>12345,
                "distance" => 20
            ],
            [
                'name' => [
                    'en' => 'Delivery Boy 4',
                    'ar' =>'سائق 4'
                ],
                'email' => "delivery.boy4@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'driving_license' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'mobile'=>"+918469435334",
                "mobile_verified"=>true,
                'latitude' => 37.415458,
                'longitude' => -122.074953,
                "category_id"=>2,
                "agency_name" => 'وكالة غاز',
                "car_number"=>12356,
                "distance" => 14
            ],
            [
                'name' => [
                    'en' => 'Delivery Boy 5',
                    'ar' =>'سائق 5'
                ],
                'email' => "delivery.boy5@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'driving_license' => 'uploads/deliveryBoys/avatar_url/1.jpg',
                'latitude' => 37.421617,
                'longitude' => -122.096288,
                "category_id"=>1,
                "agency_name" => '',
                "car_number"=>123567,
                "distance" => 10
            ],
        ];

        foreach ($deliveryBoys as $deliveryBoy) {
            DeliveryBoy::create($deliveryBoy);
        }
    }
}
