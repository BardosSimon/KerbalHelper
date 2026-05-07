<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'celestial_body_id',
        'name',
        'slug',
        'achievement_type',
        'description',
        'recommended_delta_v_mps',
        'minimum_distance_km',
        'science_reward',
        'funds_reward',
        'difficulty',
        'sort_order',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function celestialBody(): BelongsTo
    {
        return $this->belongsTo(CelestialBody::class);
    }

    public function requiredAchievements(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramAchievement::class,
            'achievement_dependencies',
            'achievement_id',
            'required_achievement_id'
        )->withTimestamps();
    }

    public function unlocksAchievements(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramAchievement::class,
            'achievement_dependencies',
            'required_achievement_id',
            'achievement_id'
        )->withTimestamps();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserAchievementProgress::class);
    }
}
