<?php

namespace Tests\Feature\Api\V1\Workout;

use App\Models\Action;
use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Group;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('workout')]
class WorkoutStoreTest extends TestCase
{
    use RefreshDatabase;

    public function testStore(): void
    {
        $user = User::factory()->create();

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
                'order' => 1,
                'sets_number' => 3,
                'repetitions' => 12,
            ]);

        $response = $this
            ->actingAs($user)
            ->json('POST', '/api/v1/workouts', ['template_id' => $template->id]);

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
                        'sets_number' => 3,
                        'repetitions' => 12,
                    ],
                ],
            ],
        ]);
    }

    #[DataProvider('invalidParams')]
    public function testStoreValidationError(array $params, array $errors): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->json('POST', '/api/v1/workouts', $params);

        $response->assertStatus(422);
        $response->assertJson(['errors' => $errors]);
    }

    public static function invalidParams(): array
    {
        return [
            [
                'params' => [
                    'template_id' => null,
                ],
                'errors' => [
                    'template_id' => [
                        'The template id field is required.'
                    ],
                ],
            ],
            [
                'params' => [
                    'template_id' => 'abc',
                ],
                'errors' => [
                    'template_id' => [
                        'The template id field must be an integer.'
                    ],
                ],
            ],
            [
                'params' => [
                    'template_id' => 123,
                ],
                'errors' => [
                    'template_id' => [
                        'The selected template id is invalid.'
                    ],
                ],
            ],
        ];
    }
}
