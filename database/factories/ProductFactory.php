<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Brand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->randomFloat(2, 100000, 5000000),
            'category_id' => Category::inRandomOrder()->value('id') ?? 1,
            'brand_id' => Brand::inRandomOrder()->value('id') ?? 1,
            'image_main' => $this->faker->imageUrl(640, 480, 'products', true),
            'is_new' => $this->faker->boolean(40),
            'is_on_sale' => $this->faker->boolean(30),
            'status' => $this->faker->boolean(90),
        ];
    }
}
