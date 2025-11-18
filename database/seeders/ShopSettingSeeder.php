<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShopSetting;

class ShopSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ tạo nếu chưa có setting
        if (ShopSetting::count() === 0) {
            ShopSetting::create([
                'logo' => null,
                'hotline' => '0337077804',
                'email' => 'ageshopvn@gmail.com',
                'address' => 'Số 10 Bất Bạt, Ba Vì, Hà Nội',
                'facebook' => null,
                'zalo' => null,
                'tiktok' => null,
               
            ]);
        }
    }
}
