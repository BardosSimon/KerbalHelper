<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramAchievementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'celestial_body_id' => $this->celestial_body_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'achievement_type' => $this->achievement_type,
            'description' => $this->description,
            'recommended_delta_v_mps' => $this->recommended_delta_v_mps,
            'minimum_distance_km' => $this->minimum_distance_km,
            'science_reward' => $this->science_reward,
            'funds_reward' => $this->funds_reward,
            'difficulty' => $this->difficulty,
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata,
            'celestial_body' => new CelestialBodyResource($this->whenLoaded('celestialBody')),
            'required_achievements' => ProgramAchievementResource::collection($this->whenLoaded('requiredAchievements')),
            'unlocks_achievements' => ProgramAchievementResource::collection($this->whenLoaded('unlocksAchievements')),
            'progress' => UserAchievementProgressResource::collection($this->whenLoaded('progress')),
        ];
    }
}
