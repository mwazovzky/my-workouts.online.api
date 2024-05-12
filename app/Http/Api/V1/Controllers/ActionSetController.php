<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Api\V1\Requests\StoreActionSetRequest;
use App\Http\Api\V1\Requests\UpdateActionSetRequest;
use App\Http\Api\V1\Resources\SetResource;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Set;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionSetController extends Controller
{
    public function store(StoreActionSetRequest $request, Action $action): JsonResource
    {
        $attributes = $request->validated();
        $set = $action->sets()->create($attributes);

        return SetResource::make($set);
    }

    public function update(UpdateActionSetRequest $request, Action $action, Set $set): JsonResource
    {
        $attributes = $request->validated();
        $set->update($attributes);

        return SetResource::make($set);
    }

    public function destroy(Action $action, Set $set): JsonResponse
    {
        $set->delete();

        return response()->json([], 204);
    }
}
