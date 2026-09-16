<?php

namespace Database\Seeders;

use App\Models\LoanHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OverdueLoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LoanHistory::create([
            'user_id' => 5,
            'asset_id' => 1,
            'loan_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(7)->toDateString(),
            'return_date' => null
        ]);
    }
}
