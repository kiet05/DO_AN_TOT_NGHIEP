<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->value('id') ?? 1,
            'sku' => strtoupper($this->faker->bothify('SKU-####-??')),
            'price' => $this->faker->randomFloat(2, 100000, 2000000),
            'quantity' => $this->faker->numberBetween(10, 100),
            'status' => $this->faker->boolean(90) ? 1 : 0,
        ];
    }
}
