<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            LoanCategorySeeder::class,
            UserSeeder::class,
            AssetSeeder::class,
            LoanHistorySeeder::class,
            ConsumableHistorySeeder::class,
        ]);
    }
}
