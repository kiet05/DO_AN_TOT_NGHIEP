<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\AttributeValue;
use App\Models\ProductVariantAttribute;

class ProductVariantAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variants = ProductVariant::all();
        $values = AttributeValue::all();

        if ($variants->count() && $values->count()) {
            foreach ($variants as $variant) {
                // Gán ngẫu nhiên 1–2 thuộc tính cho mỗi biến thể
                $randomValues = $values->random(rand(1, 2));
                foreach ($randomValues as $val) {
                    ProductVariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'attribute_value_id' => $val->id,
                    ]);
                }
            }
        }
    }
}
