<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Voucher;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'discount_value' => $type === 'percentage'
                ? $this->faker->numberBetween(5, 50)   
                : $this->faker->randomFloat(2, 10000, 500000),
            'usage_limit' => $this->faker->numberBetween(10, 200),
            'status' => $this->faker->boolean(90),
            'expired_at' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}
