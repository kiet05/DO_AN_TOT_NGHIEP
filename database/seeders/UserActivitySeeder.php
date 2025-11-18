<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserActivitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_activities')->insert([
            [
                'user_id'    => 1,                  // khách hàng bị tác động
                'causer_id'  => 1,                  // admin thực hiện
                'action'     => 'login',
                'ip'         => '127.0.0.1',
                'user_agent' => 'Chrome Windows',
                'payload'    => json_encode(['note' => 'Người dùng đăng nhập']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 1,
                'causer_id'  => 1,
                'action'     => 'view_customer_list',
                'ip'         => '127.0.0.1',
                'user_agent' => 'Chrome Windows',
                'payload'    => json_encode(['page' => 'Danh sách khách hàng']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 2,
                'causer_id'  => 1,
                'action'     => 'update_profile',
                'ip'         => '127.0.0.1',
                'user_agent' => 'Chrome Windows',
                'payload'    => json_encode(['field' => 'name']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
