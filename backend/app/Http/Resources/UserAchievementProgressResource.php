<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAchievementProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'program_achievement_id' => $this->program_achievement_id,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'vessel_name' => $this->vessel_name,
            'actual_delta_v_mps' => $this->actual_delta_v_mps,
            'closest_approach_km' => $this->closest_approach_km,
            'notes' => $this->notes,
            'achievement' => new ProgramAchievementResource($this->whenLoaded('achievement')),
        ];
    }
}
