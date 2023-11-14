<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managers = [
            [
                'name' => [
                    'en' => 'Manager 1',
                    'ar' => 'مدير 1'
                ],
                'email' => "manager@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                "avatar_url"=> "uploads/managers/avatar_url/1.jpg",
                "license"=> "uploads/managers/license/2.jpg",
                'mobile'=>"+918469435337",
                "mobile_verified"=>true,
                "is_approval" => 1
            ],
            [
                'name' => [
                    'en' => 'rahaf manager',
                    'ar' => 'المديرة رهف'
                ],
                'email' => "rahaf@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                "avatar_url"=> "uploads/managers/avatar_url/1.jpg",
                "license"=> "uploads/managers/license/2.jpg",
                'mobile'=>"123456789",
                "mobile_verified"=>true ,
                "is_approval" => 1
            ]
        ];

        foreach ($managers as $manager) {
            Manager::create($manager);
        }
    }
}
