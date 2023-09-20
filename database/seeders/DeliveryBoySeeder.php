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
                'name' => "Delivery Boy 1",// Charles Jones
                'email' => "delivery.boy@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'delivery_boy_avatars/1.jpeg',
                'latitude' => 37.421104,
                'longitude' => -122.086951,
                'mobile'=>"+918469435337",
                "mobile_verified"=>true,
                "category_id"=>1,
                "car_number"=>123
            ],
            [
                'name' => "Delivery Boy 2",// David Miller
                'email' => "delivery.boy2@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'delivery_boy_avatars/2.jpeg',
                'mobile'=>"+918469435336",
                "mobile_verified"=>true,
                'latitude' => 37.419010,
                'longitude' => -122.077957,
                "category_id"=>1,
                "car_number"=>1234
            ],
            [
                'name' => "Delivery Boy 3",// John Taylor
                'email' => "delivery.boy3@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'delivery_boy_avatars/3.jpg',
                'mobile'=>"+918469435335",
                "mobile_verified"=>true,
                'latitude' => 37.416797,
                'longitude' => -122.082967,
                "category_id"=>1,
                "car_number"=>12345
            ],
            [
                'name' => "Delivery Boy 4",// Benjamin Lopez
                'email' => "delivery.boy4@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'delivery_boy_avatars/4.jpg',
                'mobile'=>"+918469435334",
                "mobile_verified"=>true,
                'latitude' => 37.415458,
                'longitude' => -122.074953,
                "category_id"=>1,
                "car_number"=>12356
            ],
            [
                'name' => "Delivery Boy 5",// Alexander Ray
                'email' => "delivery.boy5@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'delivery_boy_avatars/5.jpg',

                'latitude' => 37.421617,
                'longitude' => -122.096288,
                "category_id"=>1,
                "car_number"=>123567
            ],
        ];

        foreach ($deliveryBoys as $deliveryBoy) {
            DeliveryBoy::create($deliveryBoy);
        }
    }
}
