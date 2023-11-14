<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => "User 1",// William Clark
                'email' => "user@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'user_avatars/1.jpeg',
                'mobile'=>"+918469435337",
                "mobile_verified"=>true,
                "account_type" => 0
            ],
            [
                'avatar_url' => 'user_avatars/2.jpeg',
                'name' => "User 2", // James Perez
                'email' => "user2@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'mobile'=>"+918469435336",
                "mobile_verified"=>true,
                "account_type" => 1
            ],
            [
                'name' => "User 3",// Olivia Austin
                'email' => "user3@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'user_avatars/3.jpeg',
                'mobile'=>"+918469435335",
                "mobile_verified"=>true,
                "account_type" => 0
            ],
            [
                'name' => "User 4",// Hannah Wilson
                'email' => "user4@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'user_avatars/4.jpeg',
                'mobile'=>"+918469435334",
                "mobile_verified"=>true,
                "account_type" => 1
            ],
            [
                'name' => "User rahaf",// Henry Martin
                'email' => "rahaf@demo.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'avatar_url' => 'user_avatars/5.jpeg',
                'mobile'=>"23456789",
                "mobile_verified"=>true,
                "account_type" => 0
            ],

        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
