<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'celestial_body_id' => ['required', 'exists:celestial_bodies,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:program_achievements,slug'],
            'achievement_type' => ['required', Rule::in(['flyby', 'orbit', 'probe', 'landing', 'return', 'science'])],
            'description' => ['nullable', 'string'],
            'recommended_delta_v_mps' => ['nullable', 'integer', 'min:0'],
            'minimum_distance_km' => ['nullable', 'integer', 'min:0'],
            'science_reward' => ['sometimes', 'integer', 'min:0'],
            'funds_reward' => ['sometimes', 'integer', 'min:0'],
            'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard', 'extreme'])],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
            'required_achievement_ids' => ['sometimes', 'array'],
            'required_achievement_ids.*' => ['exists:program_achievements,id'],
        ];
    }
}
