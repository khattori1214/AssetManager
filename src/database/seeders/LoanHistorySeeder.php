<?php

namespace Database\Seeders;

use App\Models\LoanHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;


class LoanHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanHistory::create([
            'user_id' => 1,
            'asset_id' => 2,
            'loan_date' => Carbon::now()->subDays(10),
            'due_date' => Carbon::now()->addDays(4)->toDateString(),
            'return_date' => null,
        ]);

        // 返却済みデータ
        LoanHistory::create([
            'user_id' => 1,
            'asset_id' => 3,
            'loan_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(6)->toDateString(),
            'return_date' => Carbon::now()->subDays(8),
        ]);
    }
}
