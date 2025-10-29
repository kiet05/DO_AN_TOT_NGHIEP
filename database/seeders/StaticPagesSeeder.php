<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class StaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['key' => 'about'],
            [
                'title' => 'Giới thiệu',
                'content' => '<p>Nội dung giới thiệu công ty/website của bạn.</p>',
                'published' => true,
            ]
        );

        Page::updateOrCreate(
            ['key' => 'contact'],
            [
                'title' => 'Liên hệ',
                'content' => '<p>Thông tin liên hệ, địa chỉ, email, số điện thoại...</p>',
                'published' => true,
            ]
        );
    }
}
