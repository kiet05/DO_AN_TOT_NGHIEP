<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Wallet;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalDeposit = $this->faker->randomFloat(2, 100000, 5000000);
        $totalSpent = $this->faker->randomFloat(2, 0, $totalDeposit);
        $balance = $totalDeposit - $totalSpent;

        return [
            'user_id' => User::factory(),
            'balance' => $balance,
            'total_deposit' => $totalDeposit,
            'total_spent' => $totalSpent,
            'last_transaction_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
