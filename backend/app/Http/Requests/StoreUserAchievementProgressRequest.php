<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAchievementProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'program_achievement_id' => ['required', 'exists:program_achievements,id'],
            'is_completed' => ['sometimes', 'boolean'],
            'completed_at' => ['nullable', 'date'],
            'vessel_name' => ['nullable', 'string', 'max:255'],
            'actual_delta_v_mps' => ['nullable', 'integer', 'min:0'],
            'closest_approach_km' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
