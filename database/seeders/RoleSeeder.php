<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Quản trị viên', 'slug' => 'admin', 'description' => 'Toàn quyền quản lý hệ thống'],
            ['name' => 'Nhân viên', 'slug' => 'staff', 'description' => 'Quản lý sản phẩm và đơn hàng'],
            ['name' => 'Khách hàng', 'slug' => 'customer', 'description' => 'Người dùng bình thường'],
            ['name' => 'Biên tập viên', 'slug' => 'editor', 'description' => 'Quản lý nội dung trang web'],
            ['name' => 'Kế toán', 'slug' => 'accountant', 'description' => 'Quản lý doanh thu và hóa đơn'],
        ];

        // Không tạo trùng slug — update nếu đã tồn tại
        DB::table('roles')->upsert(
            $roles,
            ['slug'],              // unique key
            ['name', 'description'] // cột sẽ được update nếu trùng
        );
    }
}
