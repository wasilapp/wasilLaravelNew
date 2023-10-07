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
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'type' => 'water',
                'commesion' => 0.1,
                'image_url' => "uploads/categories-icons/1.jpeg",
            ],
            [
                'title' => [
                    'en' => 'Gas Service',
                    'ar' =>'خدمة الغاز'
                ],
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'type' => 'gas',
                'commesion' => 0.1,
                'image_url' => "uploads/categories-icons/2.jpeg",
            ],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
