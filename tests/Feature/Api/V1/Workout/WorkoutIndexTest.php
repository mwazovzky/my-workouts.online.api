<?php

namespace Tests\Feature\Api\V1\Workout;

use App\Models\Action;
use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Group;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('workout')]
class WorkoutIndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        $user = User::factory()->create();

        $workout = Workout::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $exerciseOne = Exercise::factory()->create([
            'name' => 'Squats',
            'group_id' => Group::factory()->create(['name' => 'Quadriceps']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Barbell']),
        ]);

        $exerciseTwo = Exercise::factory()->create([
            'name' => 'Crunches',
            'group_id' => Group::factory()->create(['name' => 'Back']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Bench']),
        ]);

        Action::factory()
            ->for($workout, 'actionable')
            ->for($exerciseOne, 'exercise')
            ->create([
                'order' => 1,
                'sets_number' => 3,
                'repetitions' => 12,
            ]);

        Action::factory()
            ->for($workout, 'actionable')
            ->for($exerciseTwo, 'exercise')
            ->create([
                'order' => 2,
                'sets_number' => 2,
                'repetitions' => 20,
            ]);

        $response = $this
            ->actingAs($user)
            ->json('GET', '/api/v1/workouts');

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
                            'repetitions' => 12,
                        ],
                        [
                            'name' => 'Crunches',
                            'description' => null,
                            'group_name' => 'Back',
                            'equipment_name' => 'Bench',
                            'order' => 2,
                            'sets_number' => 2,
                            'repetitions' => 20,
                        ],
                    ]
                ],
            ],
        ]);
    }
}
