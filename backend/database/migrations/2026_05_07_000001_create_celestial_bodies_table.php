<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('celestial_bodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('celestial_bodies')->nullOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('body_type');
            $table->unsignedInteger('radius_km')->nullable();
            $table->unsignedBigInteger('semi_major_axis_km')->nullable();
            $table->unsignedInteger('sphere_of_influence_km')->nullable();
            $table->decimal('surface_gravity_g', 6, 3)->nullable();
            $table->boolean('has_atmosphere')->default(false);
            $table->unsignedInteger('atmosphere_height_m')->nullable();
            $table->unsignedInteger('low_orbit_altitude_m')->nullable();
            $table->unsignedInteger('delta_v_from_kerbin_low_orbit_mps')->nullable();
            $table->unsignedInteger('delta_v_landing_mps')->nullable();
            $table->unsignedInteger('delta_v_return_mps')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celestial_bodies');
    }
};
