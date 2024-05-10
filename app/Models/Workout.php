<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actions(): MorphMany
    {
        return $this->morphMany(Action::class, 'actionable')->orderBy('order');
    }

    public static function createFromTemplate(User $user, Template $template): static
    {
        $workout = static::create([
            'name' => $template->name,
            'description' => $template->description,
            'user_id' => $user->id,
        ]);

        foreach ($template->actions->sortBy('order') as $referenceAction) {
            $action = $workout->actions()->create([
                'order' => $referenceAction->order,
                'sets_number' => $referenceAction->sets_number,
                'repetitions' => $referenceAction->repetitions,
                'exercise_id' => $referenceAction->exercise_id,
            ]);

            for ($count = 1; $count <= $action->sets_number; $count++) {
                $action->sets()->create([
                    'number' => $count,
                    'repetitions' => $action->repetitions,
                ]);
            }
        }

        return $workout;
    }

    public static function createFromWorkout(User $user, Workout $referenceWorkout): static
    {
        $workoutAttributes = ['name', 'description'];
        $actionAttributes = ['order', 'sets_number', 'repetitions', 'exercise_id'];
        $setAttributes = ['number', 'weight', 'repetitions'];

        $workout = static::create([
            ...$referenceWorkout->only($workoutAttributes),
            'user_id' => $user->id,
        ]);

        foreach ($referenceWorkout->actions as $referenceAction) {
            $action = $workout->actions()->create($referenceAction->only($actionAttributes));

            foreach ($referenceAction->sets as $referenceSet) {
                $action->sets()->create($referenceSet->only($setAttributes));
            }
        }

        return $workout;
    }
}
