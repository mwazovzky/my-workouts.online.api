<?php

namespace Tests\Feature\Api\V1\Set;

use App\Models\Action;
use App\Models\Set;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;
use Tests\TestCase;

#[AttributesGroup('set')]
class SetDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', "/api/v1/actions/{$action->id}/sets/{$set->id}");

        $response->assertStatus(204);

        $this->assertModelMissing($set);
    }

    public function testDestroyActionNotFoundError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($action)->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', "/api/v1/actions/123/sets/{$set->id}");

        $response->assertStatus(404);
    }

    public function testDestroySetNotFoundError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', "/api/v1/actions/{$action->id}/sets/123");

        $response->assertStatus(404);
    }

    public function testDestroySetDoesNotBelongToActionError(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create();
        $action = Action::factory()->for($workout, 'actionable')->create();
        $otherAction = Action::factory()->for($workout, 'actionable')->create();
        $set = Set::factory()->for($otherAction)->create();

        $response = $this
            ->actingAs($user)
            ->json('DELETE', "/api/v1/actions/{$action->id}/sets/{$set->id}");

        $response->assertStatus(404);
    }
}
