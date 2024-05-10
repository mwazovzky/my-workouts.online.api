<?php

namespace App\Http\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'weight' => $this->weight,
            'repetitions' => $this->repetitions,
            'is_completed' => $this->is_completed,
        ];
    }
}
