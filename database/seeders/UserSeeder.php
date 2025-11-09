<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy role_id của role 'admin' (nên có sẵn từ RoleSeeder)
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

        // Nếu chưa có role admin, có thể thoát hoặc tự tạo nhanh (chọn 1 trong 2)
        if (!$adminRoleId) {
            // C1: thoát (an toàn, tránh hardcode)
            // throw new \RuntimeException("Role 'admin' chưa tồn tại. Hãy chạy RoleSeeder trước.");

            // C2: hoặc tự tạo nhanh (bật nếu muốn)
            // $adminRoleId = DB::table('roles')->insertGetId([
            //     'name' => 'Quản trị viên',
            //     'slug' => 'admin',
            //     'description' => 'Toàn quyền hệ thống',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
        }

        // Tạo/cập nhật user Admin theo email (idempotent)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // khóa duy nhất
            [
                'name'     => 'Admin',
                'slug'     => 'admin',                   // giữ slug cố định
                'password' => Hash::make('123456'),      // đổi sau khi login
                'role_id'  => $adminRoleId,
            ]
        );
    }
}
