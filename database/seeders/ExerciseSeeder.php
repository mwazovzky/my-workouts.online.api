<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include __DIR__ . '/' . 'data/exercises.php';

        foreach ($data as $item) {
            $group = Group::where('name', $item['group'])->firstOrFail();
            $equipment = Equipment::where('name', $item['equipment'])->firstOrFail();

            Exercise::factory()->create([
                'name' => $item['name'],
                'group_id' => $group->id,
                'equipment_id' => $equipment->id,
            ]);
        }
    }
}
