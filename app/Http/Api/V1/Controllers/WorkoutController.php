<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Api\V1\Requests\ReplicateWorkoutRequest;
use App\Http\Api\V1\Requests\StoreWorkoutRequest;
use App\Http\Api\V1\Resources\WorkoutResource;
use App\Http\Api\V1\Resources\WorkoutShowResource;
use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    public function index(): JsonResource
    {
        $workouts = Workout::with(['actions' => function ($query) {
            $query->with(['exercise' => ['group', 'equipment']]);
        }])->get();

        return WorkoutResource::collection($workouts);
    }

    public function show(Workout $workout): JsonResource
    {
        return WorkoutShowResource::make($workout);
    }

    public function store(StoreWorkoutRequest $request): JsonResource
    {
        $user = Auth::user();
        $templateId = $request->validated('template_id');
        $template = Template::find($templateId);
        $workout = Workout::createFromTemplate($user, $template);

        return WorkoutResource::make($workout);
    }

    public function replicate(ReplicateWorkoutRequest $request): JsonResource
    {
        $user = Auth::user();
        $originalId = $request->validated('workout_id');
        $original = Workout::find($originalId);
        $workout = Workout::createFromWorkout($user, $original);

        return WorkoutResource::make($workout);
    }

    public function destroy(Workout $workout): JsonResponse
    {
        $workout->actions()->delete();
        $workout->delete();

        return response()->json([], 204);
    }
}
