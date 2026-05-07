<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CelestialBodyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'body_type' => $this->body_type,
            'radius_km' => $this->radius_km,
            'semi_major_axis_km' => $this->semi_major_axis_km,
            'sphere_of_influence_km' => $this->sphere_of_influence_km,
            'surface_gravity_g' => $this->surface_gravity_g,
            'has_atmosphere' => $this->has_atmosphere,
            'atmosphere_height_m' => $this->atmosphere_height_m,
            'low_orbit_altitude_m' => $this->low_orbit_altitude_m,
            'delta_v_from_kerbin_low_orbit_mps' => $this->delta_v_from_kerbin_low_orbit_mps,
            'delta_v_landing_mps' => $this->delta_v_landing_mps,
            'delta_v_return_mps' => $this->delta_v_return_mps,
            'metadata' => $this->metadata,
            'parent' => new CelestialBodyResource($this->whenLoaded('parent')),
            'moons' => CelestialBodyResource::collection($this->whenLoaded('moons')),
            'achievements' => ProgramAchievementResource::collection($this->whenLoaded('achievements')),
        ];
    }
}
