<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'title' => [
                    'en' => 'Water Service',
                    'ar' =>'خدمة الماء'
                ],
                'type' => 'water',
                'commesion' => 0.1,
                'image_url' => "categories/OSm7NpPLrSyIfqoe4QM6BTBQieVPsgmz5l04vjXG.png",
            ],
            [
                'title' => [
                    'en' => 'Gas Service',
                    'ar' =>'خدمة الغاز'
                ],
                'type' => 'gas',
                'commesion' => 0.1,
                'image_url' => "categories/68nhUGKvinf6u5KzcPKeaPs6bOEz56ZpSWqbAue7.png",
            ],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
