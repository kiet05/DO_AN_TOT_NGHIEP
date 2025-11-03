<?php

namespace Database\Seeders;

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
            // Người dùng
            ['name' => 'Xem người dùng', 'slug' => 'view-users', 'description' => 'Cho phép xem danh sách người dùng'],
            ['name' => 'Thêm người dùng', 'slug' => 'create-users', 'description' => 'Cho phép thêm người dùng mới'],
            ['name' => 'Sửa người dùng', 'slug' => 'edit-users', 'description' => 'Cho phép sửa thông tin người dùng'],
            ['name' => 'Xóa người dùng', 'slug' => 'delete-users', 'description' => 'Cho phép xóa người dùng'],

            // Vai trò & quyền
            ['name' => 'manage_roles', 'slug' => 'manage-roles', 'description' => 'Quản lý vai trò và phân quyền'],

            // Sản phẩm
            ['name' => 'Xem sản phẩm', 'slug' => 'view-products', 'description' => 'Cho phép xem sản phẩm'],
            ['name' => 'Thêm sản phẩm', 'slug' => 'create-products', 'description' => 'Cho phép thêm sản phẩm mới'],
            ['name' => 'Sửa sản phẩm', 'slug' => 'edit-products', 'description' => 'Cho phép sửa sản phẩm'],
            ['name' => 'Xóa sản phẩm', 'slug' => 'delete-products', 'description' => 'Cho phép xóa sản phẩm'],
            ['name' => 'manage_products', 'slug' => 'manage-products', 'description' => 'Quản lý sản phẩm'],

            // Đơn hàng
            ['name' => 'Xem đơn hàng', 'slug' => 'view-orders', 'description' => 'Cho phép xem danh sách đơn hàng'],
            ['name' => 'Cập nhật đơn hàng', 'slug' => 'update-orders', 'description' => 'Cho phép cập nhật trạng thái đơn hàng'],
            ['name' => 'manage_orders', 'slug' => 'manage-orders', 'description' => 'Quản lý đơn hàng'],

            // Đánh giá & Voucher
            ['name' => 'manage_reviews', 'slug' => 'manage-reviews', 'description' => 'Duyệt và quản lý đánh giá'],
            ['name' => 'manage_vouchers', 'slug' => 'manage-vouchers', 'description' => 'Quản lý mã giảm giá'],

            // Báo cáo
            ['name' => 'view_reports', 'slug' => 'view-reports', 'description' => 'Xem báo cáo thống kê'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
