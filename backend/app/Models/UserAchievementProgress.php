<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievementProgress extends Model
{
    use HasFactory;

    protected $table = 'user_achievement_progress';

    protected $fillable = [
        'user_id',
        'program_achievement_id',
        'is_completed',
        'completed_at',
        'vessel_name',
        'actual_delta_v_mps',
        'closest_approach_km',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(ProgramAchievement::class, 'program_achievement_id');
    }
}
