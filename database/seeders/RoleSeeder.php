<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['name' => 'Quản trị viên', 'slug' => 'admin', 'description' => 'Toàn quyền quản lý hệ thống'],
            ['name' => 'Nhân viên', 'slug' => 'staff', 'description' => 'Quản lý sản phẩm và đơn hàng'],
            ['name' => 'Khách hàng', 'slug' => 'customer', 'description' => 'Người dùng bình thường'],
            ['name' => 'Biên tập viên', 'slug' => 'editor', 'description' => 'Quản lý nội dung trang web'],
            ['name' => 'Kế toán', 'slug' => 'accountant', 'description' => 'Quản lý doanh thu và hóa đơn'],
        ]);
    }
}
