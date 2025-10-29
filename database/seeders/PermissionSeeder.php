<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> origin/feature/orders
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
<<<<<<< HEAD
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
=======
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
>>>>>>> origin/feature/orders
    }
}
