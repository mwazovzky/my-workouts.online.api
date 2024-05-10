<?php

namespace Tests\Feature\Api\V1\Workout;

use App\Models\Action;
use App\Models\Set;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;


#[AttributesGroup('workout')]
class WorkoutDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testDelete(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', '/api/v1/workouts/' . $workout->id);

        $response->assertStatus(204);

        $this->assertModelMissing($workout);
        $this->assertModelMissing($action);
        $this->assertModelMissing($set);
    }

    public function testDeleteModelNotFoundError(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', '/api/v1/workouts/123');

        $response->assertStatus(404);
    }
}
