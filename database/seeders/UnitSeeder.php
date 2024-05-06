<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include __DIR__ . '/' . 'data/units.php';

        foreach ($data as $item) {
            Unit::factory()->create($item);
        }
    }
}
