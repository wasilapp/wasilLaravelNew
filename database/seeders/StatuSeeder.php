<?php

namespace Database\Seeders;

use App\Models\Statu;
use Illuminate\Database\Seeder;

class StatuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            [
                'title' => [
                    'en' => 'ORDER WAIT FOR CONFIRMATION',
                    'ar' =>'بانتظار الموافقة'
                ]
            ],
            [
                'title' => [
                    'en' => 'ACCEPTED BY SHOP',
                    'ar' =>'تم القبول من المتجر'
                ]
            ],
            [
                'title' => [
                    'en' => 'ASSIGN THE DRIVER',
                    'ar' =>'تم الاسناد الى السائق'
                ]
            ],
            [
                'title' => [
                    'en' => 'ACCEPTED BY DRIVER',
                    'ar' =>'تم القبول من السائق'
                ]
            ],
            [
                'title' => [
                    'en' => 'ON THE WAY',
                    'ar' =>'في الطريق'
                ]
            ],
            [
                'title' => [
                    'en' => 'DELIVERED',
                    'ar' =>'تم التسليم'
                ]
            ],
            [
                'title' => [
                    'en' => 'REVIEWED',
                    'ar' =>'تم التقييم'
                ]
            ],
            [
                'title' => [
                    'en' => 'REJECTED BY SHOP',
                    'ar' =>'تم الرفض من قبل المتجر'
                ]
            ],
            [
                'title' => [
                    'en' => 'REJECTED BY DRIVER',
                    'ar' =>'تم الرفض من السائق'
                ]
            ],
            [
                'title' => [
                    'en' => 'CANCELLED BY USER',
                    'ar' =>'تم الالغاء من المستخدم'
                ]
            ],
            [
                'title' => [
                    'en' => 'CANCELLED BY SHOP',
                    'ar' =>'تم الالغاء من المتجر'
                ]
            ],
            [
                'title' => [
                    'en' => 'CANCELLED BY DRIVER',
                    'ar' =>'تم الالغاء من السائق'
                ]
            ],
        ];

        foreach ($status as $statu) {
            Statu::create($statu);
        }
    }
}
