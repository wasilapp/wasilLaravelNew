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
                    'ar' =>'اسطوانة جديدة'
                ],
                'price' => 3.4,
                'image_url' => 'sub-categories/9i6HVuI6uXBR37Rdh7MVhGrU1cCWP0ElRto39sDw.png',
                'category_id' => "2",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => '13 Coupons',
                    'ar' =>'13 كوبونات'
                ],
                'price' => 9.9,
                'image_url' => 'sub-categories/aRcLnUk5V2UPCWST88PcohtHvBlhwvQ4J3Z6LtVf.jpg',
                'category_id' => "2",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Replac Bottle',
                    'ar' =>'استبدال الزجاجة'
                ],
                'price' =>0.65,
                'image_url' => 'sub-categories/f5S0D39bIkvSFG1MuFC9xISpFyxQOdo0gc32E4BP.png',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Replac Cylinder',
                    'ar' =>'استبدال الاسطوانة'
                ],
                'price' => 6.9,
                'image_url' => 'sub-categories/fAA3pDR525fXoOJsv6Xhoru2Gxn5GxF5RBFC0e0R.png',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Medium Packages',
                    'ar' =>'الحزم المتوسطة'
                ],
                'price' => 1.9,
                'image_url' => 'sub-categories/J6qI03qoRoKfSQLXJxglKnVvNyzKP4rsKyz6Cyyb.jpg',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Small packages',
                    'ar' =>'حزم صغيرة'
                ],
                'price' => 1.9,
                'image_url' => 'sub-categories/otTwnzmxdPQyqERrPGMHndW49EidCEyXs5Iz9HKB.jpg',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Water Glasess',
                    'ar' =>'كاسات ماء'
                ],
                'price' => 1.9,
                'image_url' => 'sub-categories/TJEBpi1Yf38TTXuREzWUEXUOiqllVfTmHU20zYJa.jpg',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'New Cylinder',
                    'ar' =>'اسطوانة جديدة'
                ],
                'price' => 41.9,
                'image_url' => 'sub-categories/uZIMY8GJWxPgbONmRhTxyCajkeYFqvbs6Mi73vND.png',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => '28 Coupons',
                    'ar' =>'28 كوبونات'
                ],
                'price' => 19.9,
                'image_url' => 'sub-categories/wymF951TapO7b317ovhl5B7EVD8vAhQNPlH6qhfE.jpg',
                'category_id' => "1",
                'active' => 1,
            ],
            [
                'title' => [
                    'en' => 'Big Packages',
                    'ar' =>'الحزم الكبيرة'
                ],
                'price' => 1.4,
                'image_url' => 'sub-categories/xhnORUcsM5jkEPNuUKGBgBwqArl4OxikTZ59jI4r.jpg',
                'category_id' => "1",
                'active' => 1,
            ],
        ];

        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}
