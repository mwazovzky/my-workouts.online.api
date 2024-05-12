<?php

namespace Tests\Feature\Api\V1\Set;

use App\Models\Action;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('set')]
class SetStoreTest extends TestCase
{
    use RefreshDatabase;

    public function testStore(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();

        $response = $this
            ->actingAs($user)
            ->json('POST', "/api/v1/actions/{$action->id}/sets", [
                'number' => 1,
                'weight' => 100,
                'repetitions' => 12,
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'number' => 1,
                'weight' => 100,
                'repetitions' => 12,
                'is_completed' => false,
            ],
        ]);
    }

    public function testStoreModelNotFoundError(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->json('POST', "/api/v1/actions/123/sets", [
                'number' => 1,
                'weight' => 100,
                'repetitions' => 12,
            ]);

        $response->assertStatus(404);
    }

    #[DataProvider('invalidParams')]
    public function testStoreValidationError(array $params, array $errors): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();

        $response = $this
            ->actingAs($user)
            ->json('POST', "/api/v1/actions/{$action->id}/sets", $params);

        $response->assertStatus(422);
        $response->assertJson(['errors' => $errors]);
    }

    public static function invalidParams(): array
    {
        return [
            'number must be provided' => [
                'params' => [
                    'weight' => 100,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'number' => [
                        'The number field is required.',
                    ],
                ],
            ],
            'number can not be null' => [
                'params' => [
                    'number' => null,
                    'weight' => 100,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'number' => [
                        'The number field is required.',
                    ],
                ],
            ],
            'number can not be string' => [
                'params' => [
                    'number' => 'abc',
                    'weight' => 100,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'number' => [
                        'The number field must be an integer.',
                    ],
                ],
            ],
            'number can not be float' => [
                'params' => [
                    'number' => 1.2345,
                    'weight' => 100,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'number' => [
                        'The number field must be an integer.',
                    ],
                ],
            ],
            'weight must be provided' => [
                'params' => [
                    'number' => 1,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field is required.',
                    ],
                ],
            ],
            'weight can not be null' => [
                'params' => [
                    'number' => 1,
                    'weight' => null,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field is required.',
                    ],
                ],
            ],
            'weight can not be string' => [
                'params' => [
                    'number' => 1,
                    'weight' => 'abc',
                    'repetitions' => 12,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field must be an integer.',
                    ],
                ],
            ],
            'weight can not be float' => [
                'params' => [
                    'number' => 1,
                    'weight' => 1.2345,
                    'repetitions' => 12,
                ],
                'errors' => [
                    'weight' => [
                        'The weight field must be an integer.',
                    ],
                ],
            ],
            'repetitions must be provided' => [
                'params' => [
                    'number' => 1,
                    'weight' => 100,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field is required.',
                    ],
                ],
            ],
            'repetitions can not be null' => [
                'params' => [
                    'number' => 1,
                    'weight' => 100,
                    'repetitions' => null,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field is required.',
                    ],
                ],
            ],
            'repetitions can not be string' => [
                'params' => [
                    'number' => 1,
                    'weight' => 100,
                    'repetitions' => 'abc',
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field must be an integer.',
                    ],
                ],
            ],
            'repetitions can not be float' => [
                'params' => [
                    'number' => 1,
                    'weight' => 100,
                    'repetitions' => 1.2345,
                ],
                'errors' => [
                    'repetitions' => [
                        'The repetitions field must be an integer.',
                    ],
                ],
            ],
        ];
    }
}
