<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolePermission;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();
        $permissions = Permission::all();

        // Gán ngẫu nhiên quyền cho mỗi vai trò
        foreach ($roles as $role) {
            $assignedPermissions = $permissions->random(rand(2, 5));

            foreach ($assignedPermissions as $permission) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
