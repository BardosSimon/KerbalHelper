<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained('program_achievements')->cascadeOnDelete();
            $table->foreignId('required_achievement_id')->constrained('program_achievements')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['achievement_id', 'required_achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_dependencies');
    }
};
