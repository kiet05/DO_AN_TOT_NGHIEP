<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gán tất cả quyền cho admin
        $admin = Role::where('slug', 'admin')->first();
        $permissions = Permission::pluck('id')->toArray();
        foreach ($permissions as $permissionId) {
            DB::table('role_permissions')->insert([
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
            DB::table('role_permissions')->insert([
                'role_id' => $staff->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Khách hàng: chỉ xem đơn hàng, sản phẩm
        $customer = Role::where('slug', 'customer')->first();
        $customerPermissions = Permission::whereIn('slug', ['view-products', 'view-orders'])
            ->pluck('id')->toArray();

        foreach ($customerPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $customer->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Gán ngẫu nhiên quyền cho các role khác (nếu có)
        $otherRoles = Role::whereNotIn('slug', ['admin', 'staff', 'customer'])->get();
        $allPermissions = Permission::all();

        foreach ($otherRoles as $role) {
            $assignedPermissions = $allPermissions->random(rand(2, 5));
            foreach ($assignedPermissions as $permission) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
