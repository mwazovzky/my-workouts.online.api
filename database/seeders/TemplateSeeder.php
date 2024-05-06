<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include __DIR__ . '/' . 'data/templates.php';

        foreach ($data as $templateAttributes) {
            $template = Template::factory()->create([
                'name' => $templateAttributes['name'],
                'description' => $templateAttributes['description'],
            ]);

            foreach ($templateAttributes['exercises'] as $exerciseAttributes) {
                $exercise = Exercise::where('name', $exerciseAttributes['name'])->firstOrFail();

                $template->actions()->create([
                    'order' => $exerciseAttributes['order'],
                    'sets_number' => $exerciseAttributes['sets_number'],
                    'repetitions' => $exerciseAttributes['repetitions'],
                    'exercise_id' => $exercise->id,
                ]);
            }
        }
    }
}
