<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 100000, 1000000);
        $quantity = $this->faker->numberBetween(1, 5);
        $discount = $this->faker->randomFloat(2, 0, 50000);
        $shipping_fee = $this->faker->randomFloat(2, 0, 30000);

        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'discount' => $discount,
            'subtotal' => $price * $quantity,
            'shipping_fee' => $shipping_fee,
            'total_price' => $price * $quantity - $discount,
            'final_amount' => $price * $quantity - $discount + $shipping_fee,
            'payment_method' => $this->faker->randomElement(['COD', 'Credit Card', 'Bank Transfer']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'order_status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'status' => 'pending',
        ];
    }
}
