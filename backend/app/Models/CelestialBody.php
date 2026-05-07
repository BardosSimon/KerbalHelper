<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CelestialBody extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'body_type',
        'radius_km',
        'semi_major_axis_km',
        'sphere_of_influence_km',
        'surface_gravity_g',
        'has_atmosphere',
        'atmosphere_height_m',
        'low_orbit_altitude_m',
        'delta_v_from_kerbin_low_orbit_mps',
        'delta_v_landing_mps',
        'delta_v_return_mps',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'surface_gravity_g' => 'decimal:3',
            'has_atmosphere' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CelestialBody::class, 'parent_id');
    }

    public function moons(): HasMany
    {
        return $this->hasMany(CelestialBody::class, 'parent_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(ProgramAchievement::class);
    }
}
