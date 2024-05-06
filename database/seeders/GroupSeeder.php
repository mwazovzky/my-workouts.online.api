<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include __DIR__ . '/' . 'data/groups.php';

        foreach ($data as $item) {
            Group::factory()->create($item);
        }
    }
}
