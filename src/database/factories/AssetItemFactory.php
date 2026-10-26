<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_history_id'=>null,
            'user_id'=>1,
            'asset_id'=>1,
            'loan_date'=>null,
            'due_date'=>null,
            'return_date'=>null
        ];
    }
}
