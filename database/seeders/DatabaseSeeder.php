<?php

namespace Database\Seeders;

<<<<<<< HEAD

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

=======
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
>>>>>>> origin/feature/orders

class DatabaseSeeder extends Seeder
{
    /**
<<<<<<< HEAD

     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),

    
=======
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
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
>>>>>>> origin/feature/orders
        ]);
    }
}
