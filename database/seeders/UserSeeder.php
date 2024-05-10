<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'alex',
            'email' => 'alex@example.com',
            'password' => 'password',
        ]);
    }
}
