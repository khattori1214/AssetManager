<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 貸出資産
        Asset::create([
            'asset_name' => 'Laptop PC',
            'category_id' => 1,
            'asset_type' => 'loan',
            'stock' => null,
            'min_stock' => null,
            'unit' => '台',
            'max_request_quantity' => null,
            'monthly_request_limit' => null,
        ]);

        // 消耗品
        Asset::create([
            'asset_name' => 'ボールペン',
            'category_id' => null,
            'asset_type' => 'consumable',
            'stock' => 10,
            'min_stock' => 5,
            'unit' => '本',
            'max_request_quantity' => 2,
            'monthly_request_limit' => 1,
        ]);
    }
}
