<?php

namespace Database\Seeders;

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
            [
                'id' => 1,
                'name' => 'Quản trị viên',
                'slug' => 'admin',
                'description' => 'Toàn quyền quản lý hệ thống'
            ],
            [
                'id' => 2,
                'name' => 'Nhân viên',
                'slug' => 'staff',
                'description' => 'Quản lý sản phẩm và đơn hàng'
            ],
            [
                'id' => 3,
                'name' => 'Khách hàng',
                'slug' => 'customer',
                'description' => 'Người dùng bình thường'
            ],
            [
                'id' => 4,
                'name' => 'Biên tập viên',
                'slug' => 'editor',
                'description' => 'Quản lý nội dung trang web'
            ],
           
        ]);
    }
}
