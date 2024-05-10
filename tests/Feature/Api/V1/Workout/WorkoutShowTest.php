<?php

namespace Tests\Feature\Api\V1\Workout;

use App\Models\Action;
use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Group;
use App\Models\Set;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('workout')]
class WorkoutShowTest extends TestCase
{
    use RefreshDatabase;

    public function testShow(): void
    {
        $user = User::factory()->create();

        $workout = Workout::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $exerciseOne = Exercise::factory()->create([
            'name' => 'Crunches',
            'group_id' => Group::factory()->create(['name' => 'Back']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Bench']),
        ]);

        $actionOne = Action::factory()
            ->for($workout, 'actionable')
            ->for($exerciseOne, 'exercise')
            ->create([
                'order' => 1,
                'sets_number' => 3,
                'repetitions' => 12,
            ]);

        Set::factory()
            ->for($actionOne)
            ->create([
                'number' => 1,
                'weight' => 200,
                'repetitions' => 12,
                'is_completed' => true,
            ]);

        $exerciseTwo = Exercise::factory()->create([
            'name' => 'Squats',
            'group_id' => Group::factory()->create(['name' => 'Quadriceps']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Barbell']),
        ]);

        $actionTwo = Action::factory()
            ->for($workout, 'actionable')
            ->for($exerciseTwo, 'exercise')
            ->create([
                'order' => 2,
                'sets_number' => 2,
                'repetitions' => 20,
            ]);

        Set::factory()
            ->for($actionTwo)
            ->create([
                'number' => 1,
                'weight' => 100,
                'repetitions' => 20,
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->json('GET', '/api/v1/workouts/' . $workout->id);

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'name' => 'Beginner',
                'description' => 'Training for beginner',
                'actions' => [
                    [
                        'name' => 'Crunches',
                        'description' => null,
                        'group_name' => 'Back',
                        'equipment_name' => 'Bench',
                        'order' => 1,
                        'sets_number' => 3,
                        'repetitions' => 12,
                        'sets' => [
                            [
                                'number' => 1,
                                'weight' => 200,
                                'repetitions' => 12,
                                'is_completed' => true,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Squats',
                        'description' => null,
                        'group_name' => 'Quadriceps',
                        'equipment_name' => 'Barbell',
                        'order' => 2,
                        'sets_number' => 2,
                        'repetitions' => 20,
                        'sets' => [
                            [
                                'number' => 1,
                                'weight' => 100,
                                'repetitions' => 20,
                                'is_completed' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[DataProvider('invalidParams')]
    public function testShowValidationError(mixed $workoutId): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->json('GET', '/api/v1/workouts/' . $workoutId);

        $response->assertStatus(404);
    }

    public static function invalidParams(): array
    {
        return [
            [
                'workoutId' => 'abc',
            ],
            [
                'workoutId' => 123,
            ],
        ];
    }
}
