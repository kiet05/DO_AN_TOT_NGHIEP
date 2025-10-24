<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'receiver_name' => $this->faker->name(),
            'receiver_phone' => $this->faker->phoneNumber(),
            'receiver_address' => $this->faker->address(),
            'shipping_fee' => $this->faker->randomFloat(2, 0, 50000),
            'total_price' => $this->faker->randomFloat(2, 100000, 10000000),
            'final_amount' => function (array $attrs) {
                return $attrs['total_price'] + $attrs['shipping_fee'];
            },
            'voucher_id' => Voucher::factory(),
            'payment_method' => $this->faker->randomElement(['COD', 'Credit Card', 'Bank Transfer']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'order_status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
        ];
    }
}
