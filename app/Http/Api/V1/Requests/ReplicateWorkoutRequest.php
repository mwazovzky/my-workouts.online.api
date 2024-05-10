<?php

namespace App\Http\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplicateWorkoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'workout_id' => [
                'required',
                'integer',
                'exists:workouts,id',
            ],
        ];
    }
}
