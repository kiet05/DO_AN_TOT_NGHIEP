<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_users', 'description' => 'Quản lý người dùng'],
            ['name' => 'manage_roles', 'description' => 'Quản lý vai trò và phân quyền'],
            ['name' => 'manage_products', 'description' => 'Quản lý sản phẩm'],
            ['name' => 'manage_orders', 'description' => 'Quản lý đơn hàng'],
            ['name' => 'manage_reviews', 'description' => 'Duyệt và quản lý đánh giá'],
            ['name' => 'manage_vouchers', 'description' => 'Quản lý mã giảm giá'],
            ['name' => 'view_reports', 'description' => 'Xem báo cáo thống kê'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
