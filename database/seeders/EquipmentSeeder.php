<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include __DIR__ . '/' . 'data/equipment.php';

        foreach ($data as $item) {
            $unit = Unit::where('label', $item['unit'])->first();

            Equipment::factory()->create([
                'name' => $item['name'],
                'description' => $item['description'],
                'unit_id' => $unit?->id,
            ]);
        }
    }
}
