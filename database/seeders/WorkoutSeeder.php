<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $template = Template::where('name', 'Beginner at the gym')->firstOrFail();
        Workout::createFromTemplate($user, $template);
    }
}
