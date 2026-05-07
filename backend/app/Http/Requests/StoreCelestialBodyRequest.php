<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCelestialBodyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:celestial_bodies,id'],
            'name' => ['required', 'string', 'max:255', 'unique:celestial_bodies,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:celestial_bodies,slug'],
            'body_type' => ['required', Rule::in(['star', 'planet', 'moon', 'dwarf_planet'])],
            'radius_km' => ['nullable', 'integer', 'min:0'],
            'semi_major_axis_km' => ['nullable', 'integer', 'min:0'],
            'sphere_of_influence_km' => ['nullable', 'integer', 'min:0'],
            'surface_gravity_g' => ['nullable', 'numeric', 'min:0'],
            'has_atmosphere' => ['sometimes', 'boolean'],
            'atmosphere_height_m' => ['nullable', 'integer', 'min:0'],
            'low_orbit_altitude_m' => ['nullable', 'integer', 'min:0'],
            'delta_v_from_kerbin_low_orbit_mps' => ['nullable', 'integer', 'min:0'],
            'delta_v_landing_mps' => ['nullable', 'integer', 'min:0'],
            'delta_v_return_mps' => ['nullable', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
