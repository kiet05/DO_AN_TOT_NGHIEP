<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->count(5)->create()->each(function ($parent) {
            // Mỗi danh mục cha có 3 danh mục con
            Category::factory()->count(3)->create([
                'parent_id' => $parent->id,
            ]);
        });
    }
}
