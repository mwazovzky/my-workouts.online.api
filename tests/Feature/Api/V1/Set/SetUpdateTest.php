<?php

namespace Tests\Feature\Api\V1\Set;

use App\Models\Action;
use App\Models\Set;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('set')]
class SetUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create(['number' => 1]);

        $response = $this
            ->actingAs($user)
            ->json('PATCH', "/api/v1/actions/{$action->id}/sets/{$set->id}", [
                'weight' => 100,
                'repetitions' => 12,
                'is_completed'  => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'number' => 1,
                'weight' => 100,
                'repetitions' => 12,
                'is_completed' => true,
            ],
        ]);
    }

    public function testUpdateActionNotFoundError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create(['number' => 1]);

        $response = $this
            ->actingAs($user)
            ->json('PATCH', "/api/v1/actions/123/sets/{$set->id}");

        $response->assertStatus(404);
    }

    public function testUpdateSetNotFoundError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create(['number' => 1]);

        $response = $this
            ->actingAs($user)
            ->json('PATCH', "/api/v1/actions/{$action->id}/sets/123");

        $response->assertStatus(404);
    }

    public function testUpdateSetDoesNotBelongToActionError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $otherAction = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($otherAction)->create(['number' => 1]);

        $response = $this
            ->actingAs($user)
            ->json('PATCH', "/api/v1/actions/{$action->id}/sets/{$set->id}");

        $response->assertStatus(404);
    }

    #[DataProvider('invalidParams')]
    public function testUpdateValidationError(array $params, array $errors): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create(['number' => 1]);

        $response = $this
            ->actingAs($user)
            ->json('PATCH', "/api/v1/actions/{$action->id}/sets/{$set->id}", $params);
        // 'weight' => 100,
        // 'repetitions' => 12,
        // 'is_completed'  => true,

        $response->assertStatus(422);
        $response->assertJson(['errors' => $errors]);
    }

    public static function invalidParams(): array
    {
        return [
            'weight must be provided' => [
                'params' => [
                    'number' => 1,
                    'repetitions' => 12,
                    'is_completed' => true,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field is required.',
                    ],
                ],
            ],
            'weight can not be null' => [
                'params' => [
                    'weight' => null,
                    'repetitions' => 12,
                    'is_completed' => true,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field is required.',
                    ],
                ],
            ],
            'weight can not be string' => [
                'params' => [
                    'weight' => 'abc',
                    'repetitions' => 12,
                    'is_completed' => true,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field must be an integer.',
                    ],
                ],
            ],
            'weight can not be float' => [
                'params' => [
                    'weight' => 1.2345,
                    'repetitions' => 12,
                    'is_completed' => true,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field must be an integer.',
                    ],
                ],
            ],
            'repetitions must be provided' => [
                'params' => [
                    'weight' => 100,
                    'is_completed' => true,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field is required.',
                    ],
                ],
            ],
            'repetitions can not be null' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => null,
                    'is_completed' => true,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field is required.',
                    ],
                ],
            ],
            'repetitions can not be string' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 'abc',
                    'is_completed' => true,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field must be an integer.',
                    ],
                ],
            ],
            'repetitions can not be float' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 1.2345,
                    'is_completed' => true,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field must be an integer.',
                    ],
                ],
            ],
            'is_completed must be provided' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'is_completed' => [
                        'The is completed field is required.',
                    ],
                ],
            ],
            'is_completed can not be null' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 12,
                    'is_completed' => null,
                ],
                'errors' => [
                    'is_completed' => [
                        'The is completed field is required.',
                    ],
                ],
            ],
            'is_completed can not be string' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 12,
                    'is_completed' => 'abc',
                ],
                'errors' => [
                    'is_completed' => [
                        'The is completed field must be true or false.',
                    ],
                ],
            ],
            'is_completed can not be float' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 1,
                    'is_completed' => 1.2345,
                ],
                'errors' => [
                    'is_completed' => [
                        'The is completed field must be true or false.',
                    ],
                ],
            ],
            'is_completed can not be integer' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 1,
                    'is_completed' => 123,
                ],
                'errors' => [
                    'is_completed' => [
                        'The is completed field must be true or false.',
                    ],
                ],
            ],
        ];
    }
}
