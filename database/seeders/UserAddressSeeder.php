<?php

namespace Database\Seeders;

use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAddresses = [
            [
                'latitude' => 37.4218855,
                'longitude' => -122.070862,
                'address' => 'A to Z Tree Nursery',
                'city' => 'Google Bay',
                'pincode' => 456789,
                'user_id' => 1
            ],
            [
                'latitude' => 37.4203822,
                'longitude' => -122.0804247,
                'address' => 'UPS Drop box',
                'city' => 'Charleston',
                'pincode' => 369852,
                'user_id' => 2
            ],
            [
                'latitude' => 37.4225616,
                'longitude' => -122.089441,
                'address' => 'Alza Vollyball Court',
                'city' => 'Googleplex',
                'pincode' => 147852,
                'user_id' => 3
            ],
            [
                'latitude' => 37.422330,
                'longitude' => -122.101335,
                'address' => 'San Antonio Rd',
                'city' => ' Palo Alto',
                'pincode' => 452033,
                'user_id' => 4
            ],
            [
                'latitude' => 37.416131,
                'longitude' => -122.092675,
                'address' => 'Rengstorff Ave',
                'city' => 'Mountain View',
                'pincode' => 240431,
                'user_id' => 5
            ],
        ];
        

        foreach ($userAddresses as $userAddress) {
            UserAddress::create($userAddress);
        }

    }
}
