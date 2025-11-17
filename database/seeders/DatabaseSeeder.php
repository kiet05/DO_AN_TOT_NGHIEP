<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Gọi RoleSeeder (nên dùng updateOrCreate trong RoleSeeder)
        $this->call([
            RoleSeeder::class,
        ]);

        // 2) Lấy role admin (nếu chưa có, bạn có thể tạo một lần nữa bằng firstOrCreate)
        $adminRole = Role::firstWhere('slug', 'admin');
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Quản trị viên',
                'slug' => 'admin',
                'description' => 'Toàn quyền quản lý hệ thống',
            ]);
        }

        // 3) Tạo hoặc update user admin (không tạo duplicate email)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // điều kiện tìm
            [
                'name' => 'Admin',
                'slug' => Str::slug('Admin'),
                'password' => Hash::make('123456'),
                'role_id' => $adminRole->id,
            ]
        );

        // 4) Gọi các seeder khác (nếu cần)
        $this->call([
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            ProductVariantSeeder::class,
            ProductVariantAttributeSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            ReviewSeeder::class,
            BannerSeeder::class,
            PaymentMethodSeeder::class,
            ShopSettingSeeder::class,
        ]);
    }
}
