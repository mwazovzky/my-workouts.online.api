<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::factory(),
            'order' => $this->faker->numberBetween(1, 10),
            'sets_number' => $this->faker->numberBetween(1, 3),
            'repetitions' => $this->faker->numberBetween(10, 20),
        ];
    }
}
