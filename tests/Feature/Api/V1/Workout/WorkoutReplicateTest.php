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
class WorkoutReplicateTest extends TestCase
{
    use RefreshDatabase;

    public function testReplicate(): void
    {
        $user = User::factory()->create();

        $originalWorkout = Workout::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $exercise = Exercise::factory()->create([
            'name' => 'Crunches',
            'group_id' => Group::factory()->create(['name' => 'Back']),
            'equipment_id' => Equipment::factory()->create(['name' => 'Bench']),
        ]);

        $action = Action::factory()
            ->for($originalWorkout, 'actionable')
            ->for($exercise, 'exercise')
            ->create([
                'order' => 1,
                'sets_number' => 2,
                'repetitions' => 12,
            ]);

        Set::factory()
            ->for($action)
            ->create([
                'number' => 1,
                'weight' => 200,
                'repetitions' => 12,
                'is_completed' => true,
            ]);

        Set::factory()
            ->for($action)
            ->create([
                'number' => 2,
                'weight' => 100,
                'repetitions' => 10,
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->json('POST', '/api/v1/workouts/replicate', ['workout_id' => $originalWorkout->id]);

        $response->assertStatus(201);
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
                        'sets_number' => 2,
                        'repetitions' => 12,
                    ],
                ],
            ],
        ]);

        $workout = Workout::with('actions')->findOrFail($response->json('data.id'));
        $sets = $workout->actions->first()->sets()->orderBy('number')->get();

        $this->assertTrue($workout->user->is($user));

        tap($sets->first(), function ($set) {
            $this->assertEquals(200, $set->weight);
            $this->assertEquals(12, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });

        tap($sets->last(), function ($set) {
            $this->assertEquals(100, $set->weight);
            $this->assertEquals(10, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });
    }

    #[DataProvider('invalidParams')]
    public function testReplicateValidationError(array $params, array $errors): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->json('POST', '/api/v1/workouts/replicate', $params);

        $response->assertStatus(422);
        $response->assertJson(['errors' => $errors]);
    }

    public static function invalidParams(): array
    {
        return [
            [
                'params' => [
                    'workout_id' => null,
                ],
                'errors' => [
                    'workout_id' => [
                        'The workout id field is required.'
                    ],
                ],
            ],
            [
                'params' => [
                    'workout_id' => 'abc',
                ],
                'errors' => [
                    'workout_id' => [
                        'The workout id field must be an integer.'
                    ],
                ],
            ],
            [
                'params' => [
                    'workout_id' => 123,
                ],
                'errors' => [
                    'workout_id' => [
                        'The selected workout id is invalid.'
                    ],
                ],
            ],
        ];
    }
}
