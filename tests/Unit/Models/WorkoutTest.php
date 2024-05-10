<?php

namespace Tests\Unit\Models;

use App\Models\Exercise;
use App\Models\Set;
use App\Models\Template;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateFromTemplate(): void
    {
        $user = User::factory()->create();

        $template = Template::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $template->actions()->createMany([
            [
                'order' => 1,
                'sets_number' => 3,
                'repetitions' => 20,
                'exercise_id' => Exercise::factory()->create()->id,
            ],
            [
                'order' => 2,
                'sets_number' => 2,
                'repetitions' => 10,
                'exercise_id' => Exercise::factory()->create()->id,
            ],
        ]);

        $workout = Workout::createFromTemplate($user, $template);

        tap($workout, function ($workout) use ($user) {
            $this->assertEquals('Beginner', $workout->name);
            $this->assertEquals('Training for beginner', $workout->description);
            $this->assertEquals($user->id, $workout->user_id);
        });

        $actions = $workout->actions;
        $this->assertCount(2, $actions);

        tap($workout->actions->first()->sets->first(), function ($set) {
            $this->assertEquals(1, $set->number);
            $this->assertNull($set->weight);
            $this->assertEquals(20, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });

        tap($workout->actions->last()->sets->last(), function ($set) {
            $this->assertEquals(2, $set->number);
            $this->assertNull($set->weight);
            $this->assertEquals(10, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });
    }

    public function testCreateFromWorkout(): void
    {
        $user = User::factory()->create();

        $referenceWorkout = Workout::factory()->create([
            'name' => 'Beginner',
            'description' => 'Training for beginner'
        ]);

        $action = $referenceWorkout->actions()->create([
            'order' => 1,
            'sets_number' => 3,
            'repetitions' => 20,
            'exercise_id' => Exercise::factory()->create()->id,
        ]);

        Set::factory()
            ->for($action)
            ->createMany([
                [
                    'number' => 1,
                    'weight' => 5,
                    'repetitions' => 12,
                    'is_completed' => true,
                ],
                [
                    'number' => 2,
                    'weight' => 4,
                    'repetitions' => 10,
                    'is_completed' => false,
                ],
            ]);

        $workout = Workout::createFromWorkout($user, $referenceWorkout);

        tap($workout, function ($workout) use ($user) {
            $this->assertEquals('Beginner', $workout->name);
            $this->assertEquals('Training for beginner', $workout->description);
            $this->assertEquals($user->id, $workout->user_id);
        });

        $this->assertCount(1, $workout->actions);

        $this->assertCount(2, $workout->actions->first()->sets);

        tap($workout->actions->first()->sets->first(), function ($set) {
            $this->assertEquals(1, $set->number);
            $this->assertEquals(5, $set->weight);
            $this->assertEquals(12, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });

        tap($workout->actions->first()->sets->last(), function ($set) {
            $this->assertEquals(2, $set->number);
            $this->assertEquals(4, $set->weight);
            $this->assertEquals(10, $set->repetitions);
            $this->assertFalse($set->is_completed);
        });
    }
}
