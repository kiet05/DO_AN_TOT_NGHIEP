<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Quản lý người dùng
            ['name' => 'Xem người dùng', 'slug' => 'view-users', 'description' => 'Cho phép xem danh sách người dùng'],
            ['name' => 'Thêm người dùng', 'slug' => 'create-users', 'description' => 'Cho phép thêm người dùng mới'],
            ['name' => 'Sửa người dùng', 'slug' => 'edit-users', 'description' => 'Cho phép sửa thông tin người dùng'],
            ['name' => 'Xóa người dùng', 'slug' => 'delete-users', 'description' => 'Cho phép xóa người dùng'],

            // Quản lý sản phẩm
            ['name' => 'Xem sản phẩm', 'slug' => 'view-products', 'description' => 'Cho phép xem sản phẩm'],
            ['name' => 'Thêm sản phẩm', 'slug' => 'create-products', 'description' => 'Cho phép thêm sản phẩm mới'],
            ['name' => 'Sửa sản phẩm', 'slug' => 'edit-products', 'description' => 'Cho phép sửa sản phẩm'],
            ['name' => 'Xóa sản phẩm', 'slug' => 'delete-products', 'description' => 'Cho phép xóa sản phẩm'],

            // Quản lý đơn hàng
            ['name' => 'Xem đơn hàng', 'slug' => 'view-orders', 'description' => 'Cho phép xem danh sách đơn hàng'],
            ['name' => 'Cập nhật đơn hàng', 'slug' => 'update-orders', 'description' => 'Cho phép cập nhật trạng thái đơn hàng'],
        ];

        \App\Models\Permission::insert($permissions);
    }
}
