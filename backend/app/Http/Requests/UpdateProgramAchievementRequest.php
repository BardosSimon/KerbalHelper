<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $achievementId = $this->route('achievement')?->id ?? $this->route('achievement');

        return [
            'celestial_body_id' => ['sometimes', 'required', 'exists:celestial_bodies,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('program_achievements', 'slug')->ignore($achievementId)],
            'achievement_type' => ['sometimes', 'required', Rule::in(['flyby', 'orbit', 'probe', 'landing', 'return', 'science'])],
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
