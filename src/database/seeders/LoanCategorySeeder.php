<?php

namespace Database\Seeders;

use App\Models\LoanCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         LoanCategory::insert([
            [
                'category_id' => 1,
                'category_name' => 'PC',
                'max_loan_days' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'category_name' => '書籍',
                'max_loan_days' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'category_name' => '機器',
                'max_loan_days' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
