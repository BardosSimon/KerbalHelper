<?php

namespace Database\Seeders;

use App\Models\CelestialBody;
use App\Models\ProgramAchievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KerbalProgramSeeder extends Seeder
{
    public function run(): void
    {
        $sun = $this->body([
            'name' => 'Kerbol',
            'body_type' => 'star',
            'radius_km' => 261600,
            'sphere_of_influence_km' => null,
            'surface_gravity_g' => 1.746,
            'has_atmosphere' => false,
            'low_orbit_altitude_m' => 610000,
            'delta_v_from_kerbin_low_orbit_mps' => 2740,
            'delta_v_landing_mps' => null,
            'delta_v_return_mps' => null,
        ]);

        $bodies = [
            ['Moho', 'planet', $sun->id, 250, 5263138, 112, 0.275, false, null, 50000, 3360, 870, 870],
            ['Eve', 'planet', $sun->id, 700, 9832684, 85109, 1.700, true, 90000, 110000, 1033, 8000, 0],
            ['Gilly', 'moon', null, 13, 31500, 126, 0.005, false, null, 10000, 620, 30, 30],
            ['Kerbin', 'planet', $sun->id, 600, 13599840, 84159, 1.000, true, 70000, 80000, 0, 3400, 0],
            ['Mun', 'moon', null, 200, 12000, 2430, 0.166, false, null, 14000, 860, 580, 580],
            ['Minmus', 'moon', null, 60, 47000, 2247, 0.050, false, null, 10000, 930, 180, 180],
            ['Duna', 'planet', $sun->id, 320, 20726155, 47922, 0.300, true, 50000, 60000, 1060, 1450, 1380],
            ['Ike', 'moon', null, 130, 3200, 1049, 0.112, false, null, 10000, 1120, 390, 390],
            ['Dres', 'dwarf_planet', $sun->id, 138, 40839348, 32833, 0.115, false, null, 10000, 2240, 430, 430],
            ['Jool', 'planet', $sun->id, 6000, 68773560, 2455985, 0.800, true, 200000, 210000, 1960, null, null],
            ['Laythe', 'moon', null, 500, 27184, 3724, 0.800, true, 50000, 60000, 1990, 2900, 2900],
            ['Vall', 'moon', null, 300, 43152, 2406, 0.235, false, null, 15000, 2160, 860, 860],
            ['Tylo', 'moon', null, 600, 68500, 10857, 0.800, false, null, 10000, 2270, 2270, 2270],
            ['Bop', 'moon', null, 65, 128500, 1221, 0.060, false, null, 10000, 1980, 220, 220],
            ['Pol', 'moon', null, 44, 179890, 1042, 0.038, false, null, 10000, 2020, 130, 130],
            ['Eeloo', 'dwarf_planet', $sun->id, 210, 90118820, 119083, 0.172, false, null, 10000, 3290, 620, 620],
        ];

        $created = ['Kerbol' => $sun];

        foreach ($bodies as [$name, $type, $parentId, $radius, $axis, $soi, $gravity, $atmosphere, $atmosphereHeight, $orbitAltitude, $transferDv, $landingDv, $returnDv]) {
            $created[$name] = $this->body([
                'name' => $name,
                'parent_id' => $parentId,
                'body_type' => $type,
                'radius_km' => $radius,
                'semi_major_axis_km' => $axis,
                'sphere_of_influence_km' => $soi,
                'surface_gravity_g' => $gravity,
                'has_atmosphere' => $atmosphere,
                'atmosphere_height_m' => $atmosphereHeight,
                'low_orbit_altitude_m' => $orbitAltitude,
                'delta_v_from_kerbin_low_orbit_mps' => $transferDv,
                'delta_v_landing_mps' => $landingDv,
                'delta_v_return_mps' => $returnDv,
            ]);
        }

        $created['Gilly']->update(['parent_id' => $created['Eve']->id]);
        $created['Mun']->update(['parent_id' => $created['Kerbin']->id]);
        $created['Minmus']->update(['parent_id' => $created['Kerbin']->id]);
        $created['Ike']->update(['parent_id' => $created['Duna']->id]);
        $created['Laythe']->update(['parent_id' => $created['Jool']->id]);
        $created['Vall']->update(['parent_id' => $created['Jool']->id]);
        $created['Tylo']->update(['parent_id' => $created['Jool']->id]);
        $created['Bop']->update(['parent_id' => $created['Jool']->id]);
        $created['Pol']->update(['parent_id' => $created['Jool']->id]);

        ProgramAchievement::whereIn('slug', [
            Str::slug('Kerbin flyby'),
            Str::slug('Kerbin return'),
        ])->delete();

        $previous = null;

        foreach ($created as $body) {
            foreach (['flyby', 'orbit', 'probe', 'landing', 'return'] as $index => $type) {
                if ($body->name === 'Kerbol' && in_array($type, ['landing', 'return'], true)) {
                    continue;
                }

                if ($body->name === 'Jool' && in_array($type, ['landing', 'return'], true)) {
                    continue;
                }

                if ($body->name === 'Kerbin' && in_array($type, ['flyby', 'return'], true)) {
                    continue;
                }

                $achievement = ProgramAchievement::updateOrCreate(
                    ['slug' => Str::slug($body->name.' '.$type)],
                    [
                        'celestial_body_id' => $body->id,
                        'name' => $body->name.' '.Str::headline($type),
                        'achievement_type' => $type,
                        'description' => $this->description($body->name, $type),
                        'recommended_delta_v_mps' => $this->recommendedDeltaV($body, $type),
                        'minimum_distance_km' => $type === 'flyby' ? max(1, (int) $body->sphere_of_influence_km) : null,
                        'science_reward' => ($index + 1) * 10,
                        'funds_reward' => ($index + 1) * 5000,
                        'difficulty' => $this->difficulty($body, $type),
                        'sort_order' => ($body->id * 10) + $index,
                    ]
                );

                if ($previous !== null && $type === 'flyby') {
                    $achievement->requiredAchievements()->syncWithoutDetaching([$previous->id]);
                }

                if ($type !== 'flyby') {
                    $required = ProgramAchievement::where('slug', Str::slug($body->name.' flyby'))->first();
                    if ($required !== null) {
                        $achievement->requiredAchievements()->syncWithoutDetaching([$required->id]);
                    }
                }

                if ($type === 'landing') {
                    $required = ProgramAchievement::where('slug', Str::slug($body->name.' probe'))->first();
                    if ($required !== null) {
                        $achievement->requiredAchievements()->syncWithoutDetaching([$required->id]);
                    }
                }

                if ($type === 'return') {
                    $required = ProgramAchievement::where('slug', Str::slug($body->name.' landing'))->first();
                    if ($required !== null) {
                        $achievement->requiredAchievements()->syncWithoutDetaching([$required->id]);
                    }
                }
            }

            $previous = ProgramAchievement::where('slug', Str::slug($body->name.' flyby'))->first() ?? $previous;
        }
    }

    private function body(array $attributes): CelestialBody
    {
        return CelestialBody::updateOrCreate(
            ['slug' => Str::slug($attributes['name'])],
            array_merge($attributes, ['slug' => Str::slug($attributes['name'])])
        );
    }

    private function description(string $body, string $type): string
    {
        return match ($type) {
            'flyby' => "Enter {$body}'s sphere of influence and record a closest approach.",
            'orbit' => "Establish a stable orbit around {$body}.",
            'probe' => "Send an uncrewed probe to {$body} and transmit science.",
            'landing' => "Land safely on {$body}.",
            'return' => "Return from {$body} to Kerbin safely.",
            default => "Complete a {$type} objective at {$body}.",
        };
    }

    private function recommendedDeltaV(CelestialBody $body, string $type): ?int
    {
        return match ($type) {
            'flyby', 'orbit', 'probe' => $body->delta_v_from_kerbin_low_orbit_mps,
            'landing' => $body->delta_v_from_kerbin_low_orbit_mps + $body->delta_v_landing_mps,
            'return' => $body->delta_v_from_kerbin_low_orbit_mps + $body->delta_v_landing_mps + $body->delta_v_return_mps,
            default => $body->delta_v_from_kerbin_low_orbit_mps,
        };
    }

    private function difficulty(CelestialBody $body, string $type): string
    {
        $deltaV = $this->recommendedDeltaV($body, $type) ?? 0;

        if ($deltaV >= 6000) {
            return 'extreme';
        }

        if ($deltaV >= 2500) {
            return 'hard';
        }

        if ($deltaV >= 1000) {
            return 'medium';
        }

        return 'easy';
    }
}
