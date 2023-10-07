<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subCategories = [
            [
                'title' => [
                    'en' => 'New Bottle',
                    'ar' =>'زجاجة جديدة'
                ],
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'price' => 3.4,
                'image_url' => 'uploads/sub_categories/1.jpeg',
                'category_id' => "1",
                'active' => 1,
                "is_approval" => 1,
                "is_primary" => 1
            ],
            [
                'title' => [
                    'en' => 'Replac Bottle',
                    'ar' =>'استبدال الزجاجة'
                ],
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'price' =>0.65,
                'image_url' => 'uploads/sub_categories/1.jpeg',
                'category_id' => "1",
                'active' => 1,
                "is_approval" => 1,
                "is_primary" => 1
            ],
            [
                'title' => [
                    'en' => 'New Cylinder',
                    'ar' =>'اسطوانة جديدة'
                ],
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'price' => 41.9,
                'image_url' => 'uploads/sub_categories/2.jpeg',
                'category_id' => "2",
                'active' => 1,
                "is_approval" => 1,
                "is_primary" => 1
            ],
            [
                'title' => [
                    'en' => 'Replac Cylinder',
                    'ar' =>'استبدال الاسطوانة'
                ],
                'description' => [
                    'en' => 'description',
                    'ar' =>'وصف'
                ],
                'price' => 6.9,
                'image_url' => 'uploads/sub_categories/2.jpeg',
                'category_id' => "2",
                'active' => 1,
                "is_approval" => 1,
                "is_primary" => 1
            ],
        ];

        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}
