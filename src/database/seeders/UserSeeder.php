<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_id' => 1,
            'employee_no' => '1',
            'user_name' => 'taro tanaka',
            'email' => 'tarotanaka@example.com',
            'password' => 'abc12345!',
            'role_id' => 1,
        ]);
    }
}
