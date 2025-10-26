<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            ['name' => 'Color', 'description' => 'Product color options'],
            ['name' => 'Size', 'description' => 'Available sizes for products'],
            ['name' => 'Material', 'description' => 'Type of material used'],
        ];

        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }
}
