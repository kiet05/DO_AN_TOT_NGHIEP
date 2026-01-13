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

    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);

        $discountValue = $type === 'percentage'
            ? $this->faker->numberBetween(5, 50)
            : $this->faker->randomFloat(2, 10000, 500000);

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,

            // Seeder/DB đang dùng discount_value
            'discount_value' => $discountValue,

            // ✅ FIX LỖI: DB bắt buộc có value (NOT NULL) nên phải đổ value
            'value' => $discountValue,

            'usage_limit' => $this->faker->numberBetween(10, 200),

            // nên để int 0/1 cho ổn định
            'status' => 1,

            'expired_at' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}