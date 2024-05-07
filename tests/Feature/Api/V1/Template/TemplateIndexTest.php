<?php

namespace Tests\Feature\Api\V1\Template;

use App\Models\Action;
use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Group;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateIndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        $template = Template::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $exerciseOne = Exercise::factory()->create([
            'name' => 'Crunches',
            'group_id' => Group::factory()->create(['name' => 'Back']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Bench']),
        ]);

        Action::factory()
            ->for($template, 'actionable')
            ->for($exerciseOne, 'exercise')
            ->create([
                'order' => 2,
                'sets_number' => 2,
                'repetitions' => 10,
            ]);

        $exerciseTwo = Exercise::factory()->create([
            'name' => 'Squats',
            'group_id' => Group::factory()->create(['name' => 'Quadriceps']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Barbell']),
        ]);

        Action::factory()
            ->for($template, 'actionable')
            ->for($exerciseTwo, 'exercise')
            ->create([
                'order' => 1,
                'sets_number' => 3,
                'repetitions' => 20,
            ]);

        $response = $this->get('/api/v1/templates');

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'name' => 'Beginner',
                    'description' => 'Training for beginner',
                    'actions' => [
                        [
                            'name' => 'Squats',
                            'description' => null,
                            'group_name' => 'Quadriceps',
                            'equipment_name' => 'Barbell',
                            'order' => 1,
                            'sets_number' => 3,
                            'repetitions' => 20,
                        ],
                        [
                            'name' => 'Crunches',
                            'description' => null,
                            'group_name' => 'Back',
                            'equipment_name' => 'Bench',
                            'order' => 2,
                            'sets_number' => 2,
                            'repetitions' => 10,
                        ],
                    ]
                ],
            ],
        ]);
    }
}
