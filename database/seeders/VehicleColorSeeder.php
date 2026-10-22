<?php

namespace Database\Seeders;

use App\Models\VehicleColor;
use Illuminate\Database\Seeder;

class VehicleColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            [
                'code' => 'solid_white',
                'name' => 'سفید ساده',
                'sort_order' => 10,
            ],
            [
                'code' => 'pearl_white',
                'name' => 'سفید صدفی',
                'sort_order' => 20,
            ],
            [
                'code' => 'solid_black',
                'name' => 'مشکی ساده',
                'sort_order' => 30,
            ],
            [
                'code' => 'metallic_black',
                'name' => 'مشکی متالیک',
                'sort_order' => 40,
            ],
            [
                'code' => 'metallic_silver',
                'name' => 'نقره‌ای متالیک',
                'sort_order' => 50,
            ],
            [
                'code' => 'gray',
                'name' => 'خاکستری',
                'sort_order' => 60,
            ],
            [
                'code' => 'metallic_gray',
                'name' => 'خاکستری متالیک',
                'sort_order' => 70,
            ],
            [
                'code' => 'metallic_dark_gray',
                'name' => 'نوک‌مدادی متالیک',
                'sort_order' => 80,
            ],
            [
                'code' => 'cream',
                'name' => 'کرم',
                'sort_order' => 90,
            ],
            [
                'code' => 'beige',
                'name' => 'بژ',
                'sort_order' => 100,
            ],
            [
                'code' => 'gold',
                'name' => 'طلایی',
                'sort_order' => 110,
            ],
            [
                'code' => 'brown',
                'name' => 'قهوه‌ای',
                'sort_order' => 120,
            ],
            [
                'code' => 'red',
                'name' => 'قرمز',
                'sort_order' => 130,
            ],
            [
                'code' => 'dark_red',
                'name' => 'زرشکی',
                'sort_order' => 140,
            ],
            [
                'code' => 'blue',
                'name' => 'آبی',
                'sort_order' => 150,
            ],
            [
                'code' => 'dark_blue',
                'name' => 'سرمه‌ای',
                'sort_order' => 160,
            ],
            [
                'code' => 'light_blue',
                'name' => 'آبی روشن',
                'sort_order' => 170,
            ],
            [
                'code' => 'green',
                'name' => 'سبز',
                'sort_order' => 180,
            ],
            [
                'code' => 'dark_green',
                'name' => 'سبز یشمی',
                'sort_order' => 190,
            ],
            [
                'code' => 'yellow',
                'name' => 'زرد',
                'sort_order' => 200,
            ],
            [
                'code' => 'orange',
                'name' => 'نارنجی',
                'sort_order' => 210,
            ],
            [
                'code' => 'purple',
                'name' => 'بنفش',
                'sort_order' => 220,
            ],
            [
                'code' => 'other',
                'name' => 'سایر',
                'sort_order' => 999,
            ],
        ];

        foreach ($colors as $color) {
            VehicleColor::updateOrCreate(
                ['code' => $color['code']],
                [
                    'name' => $color['name'],
                    'sort_order' => $color['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
