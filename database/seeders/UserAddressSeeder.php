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
                'street' => 'A to Z Tree Nursery',
                'name' => 'Address1',
                'city' => 'Google Bay',
                'apartment_num' => 456789,
                'user_id' => 1
            ],
            [
                'latitude' => 37.4203822,
                'longitude' => -122.0804247,
                'street' => 'UPS Drop box',
                'name' => 'Address2',
                'city' => 'Charleston',
                'apartment_num' => 369852,
                'user_id' => 2
            ],
            [
                'latitude' => 37.4225616,
                'longitude' => -122.089441,
                'street' => 'Alza Vollyball Court',
                'name' => 'Address3',
                'city' => 'Googleplex',
                'apartment_num' => 147852,
                'user_id' => 3
            ],
            [
                'latitude' => 37.422330,
                'longitude' => -122.101335,
                'street' => 'San Antonio Rd',
                'name' => 'Address4',
                'city' => ' Palo Alto',
                'apartment_num' => 452033,
                'user_id' => 4
            ],
            [
                'latitude' => 37.416131,
                'longitude' => -122.092675,
                'street' => 'Rengstorff Ave',
                'name' => 'Address5',
                'city' => 'Mountain View',
                'apartment_num' => 240431,
                'user_id' => 5
            ],
        ];


        foreach ($userAddresses as $userAddress) {
            UserAddress::create($userAddress);
        }

    }
}
