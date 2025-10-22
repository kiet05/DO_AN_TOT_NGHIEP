<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            'Color' => ['Red', 'Blue', 'Green', 'Black', 'White'],
            'Size' => ['S', 'M', 'L', 'XL'],
            'Material' => ['Cotton', 'Leather', 'Denim'],
        ];

        foreach ($values as $attributeName => $vals) {
            $attribute = Attribute::firstOrCreate(['name' => $attributeName]);

            foreach ($vals as $val) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $val,
                ]);
            }
        }
    }
}
