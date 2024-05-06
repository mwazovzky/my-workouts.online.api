<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UnitSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(EquipmentSeeder::class);
        $this->call(ExerciseSeeder::class);
        $this->call(TemplateSeeder::class);
    }
}
