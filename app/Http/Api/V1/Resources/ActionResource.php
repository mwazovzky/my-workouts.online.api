<?php

namespace App\Http\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    private bool $withSets = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order' => $this->order,
            'sets_number' => $this->sets_number,
            'repetitions' => $this->repetitions,

            $this->merge(ExerciseResource::make($this->exercise)),

            $this->mergeWhen($this->withSets, [
                'sets' => SetResource::collection($this->sets),
            ]),
        ];
    }

    public function setWithSets(bool $withSets): static
    {
        $this->withSets = $withSets;

        return $this;
    }
}
