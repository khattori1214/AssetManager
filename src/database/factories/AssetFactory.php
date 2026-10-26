<?php

namespace Database\Factories;
use App\Models\LoanCategory;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_name' => 'テスト用資産',
            'category_id'=>null,
            'asset_type' => 'loan',
            'unit'=>'個',
            'stock' => null,
            'min_stock' => null,
            'max_request_quantity' => null,
            'monthly_request_limit' => null
        ];
    }

    public function loan(): static
    {
        return $this->state(fn(array $attributes) => [
            'asset_type' => 'loan',
            'category_id' => LoanCategory::factory(),
        ]);
    }


    public function consumable(): static
    {
        return $this->state(fn(array $attributes) => [
            'asset_type' => 'consumable',
            'stock' => 10,
            'min_stock' => 5,
            'max_request_quantity' => 2,
            'monthly_request_limit' => 1
        ]);
    }
}
