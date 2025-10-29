<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Gán tất cả quyền cho admin
        $admin = Role::where('slug', 'admin')->first();
        $permissions = Permission::pluck('id')->toArray();
        foreach ($permissions as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $admin->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Gán quyền cơ bản cho nhân viên
        $staff = Role::where('slug', 'staff')->first();
        $staffPermissions = Permission::whereIn('slug', [
            'view-products', 'create-products', 'edit-products', 'view-orders', 'update-orders'
        ])->pluck('id')->toArray();

        foreach ($staffPermissions as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $staff->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Khách hàng: chỉ xem đơn hàng, sản phẩm
        $customer = Role::where('slug', 'customer')->first();
        $customerPermissions = Permission::whereIn('slug', ['view-products', 'view-orders'])
            ->pluck('id')->toArray();

        foreach ($customerPermissions as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $customer->id,
                'permission_id' => $permissionId,
            ]);
        }
    }
}
