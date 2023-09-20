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
                'avatar_url' => 'uploads/managers/1.jpg',
                'mobile'=>"+918469435337",
                "mobile_verified"=>true
            ],
            [
                'name' => [
                    'en' => 'Manager 2',
                    'ar' => 'مدير 2'
                ],
                'email' => "manager2@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/managers/2.jpg',
                'mobile'=>"+918469435336",
                "mobile_verified"=>true
            ],
            [
                'name' => [
                    'en' => 'Manager 3',
                    'ar' => 'مدير 3'
                ],
                'email' => "manager3@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'managers/3.jpg',
                'mobile'=>"+918469435335",
                "mobile_verified"=>true
            ],
            [
                'name' => [
                    'en' => 'Manager 4',
                    'ar' => 'مدير 4'
                ],
                'email' => "manager4@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/managers/4.jpg',
                'mobile'=>"+918469435334",
                "mobile_verified"=>true
            ],
            [
                'name' => [
                    'en' => 'Manager 5',
                    'ar' => 'مدير 5'
                ],
                'email' => "manager5@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'uploads/managers/5.jpg',

            ],
        ];
        
        foreach ($managers as $manager) {
            Manager::create($manager);
        }
    }
}
