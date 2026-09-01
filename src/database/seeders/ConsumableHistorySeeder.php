<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsumableHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConsumableHistory::create([
            'user_id' => 1,
            'asset_id' => 7,
            'request_date' => Carbon::now()->toDateString(),
            'quantity' => 1,
        ]);

        // 前月の取得履歴
        ConsumableHistory::create([
            'user_id' => 1,
            'asset_id' => 9,
            'request_date' => Carbon::now()
                ->subMonth()
                ->startOfMonth()
                ->toDateString(),
            'quantity' => 1,
        ]);
    }
}
