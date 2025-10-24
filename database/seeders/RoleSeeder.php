<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'Quản trị viên', 'description' => 'Toàn quyền quản lý hệ thống'],
            ['name' => 'Nhân viên', 'description' => 'Quản lý sản phẩm và đơn hàng'],
            ['name' => 'Khách hàng', 'description' => 'Người dùng bình thường'],
            ['name' => 'Biên tập viên', 'description' => 'Quản lý nội dung trang web'],
            ['name' => 'Kế toán', 'description' => 'Quản lý doanh thu và hóa đơn'],
        ]);
    }
}
