<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class LoanHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'asset_id' => Asset::factory()->loan(),
            'loan_date' => now(),
            'due_date' => now()->addDays(7),
            'return_date' => null
        ];
    }

    // 未返却
    public function unreturned(): static
    {
        return $this->state(fn(array $attributes) => [
            'return_date' => null,
        ]);
    }

    // 返却済み
    public function returned(): static
    {
        return $this->state(fn(array $attributes) => [
            'loan_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
            'return_date' => now()->subDay(),
        ]);
    }

    // 期限超過かつ未返却
    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'loan_date' => now()->subDays(10),
            'due_date' => now()->subDays(3),
            'return_date' => null,
        ]);
    }
}
