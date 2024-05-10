<?php

namespace App\Http\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActionCollection extends ResourceCollection
{
    private bool $withSets = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(fn ($action) => [$this->merge(ActionResource::make($action)->setWithSets($this->withSets))])
            ->toArray();
    }

    public function setWithSets(bool $withSets): static
    {
        $this->withSets = $withSets;

        return $this;
    }
}
