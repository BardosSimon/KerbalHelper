<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_achievement_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_achievement_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('vessel_name')->nullable();
            $table->unsignedInteger('actual_delta_v_mps')->nullable();
            $table->unsignedInteger('closest_approach_km')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'program_achievement_id'], 'user_achievement_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievement_progress');
    }
};
