<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\OrderItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnItem>
 */
class ReturnItemFactory extends Factory
{
    protected $model = ReturnItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'return_id' => ReturnModel::factory(),
            'order_item_id' => OrderItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'image_proof' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement([0, 1, 2]),
        ];
    }
}
