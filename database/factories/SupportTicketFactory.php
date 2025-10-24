<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? 1,
            'type' => $this->faker->randomElement(['Khiếu nại', 'Hỗ trợ đơn hàng', 'Tư vấn sản phẩm', 'Lỗi hệ thống']),
            'question' => $this->faker->sentence(12),
            'answer' => $this->faker->boolean(60) ? $this->faker->sentence(15) : null,
            'status' => $this->faker->boolean(60) ? 1 : 0,
        ];
    }
}
