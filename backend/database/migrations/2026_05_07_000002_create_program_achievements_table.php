<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('celestial_body_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('achievement_type');
            $table->text('description')->nullable();
            $table->unsignedInteger('recommended_delta_v_mps')->nullable();
            $table->unsignedInteger('minimum_distance_km')->nullable();
            $table->unsignedInteger('science_reward')->default(0);
            $table->unsignedInteger('funds_reward')->default(0);
            $table->string('difficulty')->default('medium');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['celestial_body_id', 'achievement_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_achievements');
    }
};
