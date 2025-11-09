<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('banners')->insert([
            [
                'title'       => 'Facilis qui tempore.',
                'image_url'   => 'banners/beinfpuc.jpg',  // đổi từ image -> image_url
                'link'        => 'http://murazik.com/officia-et-vel-atque-dolor',
                'position'    => 'bottom',                // phải đúng enum của bảng
                'is_active'   => 1,
                'start_date'  => now(),
                'end_date'    => now()->addDays(30),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Quam dignissimos autem.',
                'image_url'   => 'banners/banner2.jpg',
                'link'        => 'http://example.com/banner-2',
                'position'    => 'top',
                'is_active'   => 1,
                'start_date'  => now(),
                'end_date'    => now()->addDays(15),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Eius repellat pariatur.',
                'image_url'   => 'banners/banner3.jpg',
                'link'        => 'http://example.com/banner-3',
                'position'    => 'middle',
                'is_active'   => 0,
                'start_date'  => now(),
                'end_date'    => now()->addDays(60),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
