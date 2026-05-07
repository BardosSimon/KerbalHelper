<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramAchievementRequest;
use App\Http\Requests\UpdateProgramAchievementRequest;
use App\Http\Resources\CelestialBodyResource;
use App\Http\Resources\ProgramAchievementResource;
use App\Models\CelestialBody;
use App\Models\ProgramAchievement;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ProgramAchievementController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProgramAchievementResource::collection(
            ProgramAchievement::query()
                ->with(['celestialBody', 'requiredAchievements'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(StoreProgramAchievementRequest $request): ProgramAchievementResource
    {
        $validated = $request->validated();
        $requiredAchievementIds = $validated['required_achievement_ids'] ?? [];
        unset($validated['required_achievement_ids']);

        $achievement = ProgramAchievement::create($validated);
        $achievement->requiredAchievements()->sync($requiredAchievementIds);

        return new ProgramAchievementResource($achievement->load(['celestialBody', 'requiredAchievements']));
    }

    public function show(ProgramAchievement $achievement): ProgramAchievementResource
    {
        return new ProgramAchievementResource(
            $achievement->load(['celestialBody', 'requiredAchievements', 'unlocksAchievements'])
        );
    }

    public function update(UpdateProgramAchievementRequest $request, ProgramAchievement $achievement): ProgramAchievementResource
    {
        $validated = $request->validated();
        $requiredAchievementIds = $validated['required_achievement_ids'] ?? null;
        unset($validated['required_achievement_ids']);

        $achievement->update($validated);

        if ($requiredAchievementIds !== null) {
            $achievement->requiredAchievements()->sync($requiredAchievementIds);
        }

        return new ProgramAchievementResource($achievement->load(['celestialBody', 'requiredAchievements']));
    }

    public function destroy(ProgramAchievement $achievement): Response
    {
        $achievement->delete();

        return response()->noContent();
    }

    public function map(): ResourceCollection
    {
        return CelestialBodyResource::collection(
            CelestialBody::query()
                ->whereNull('parent_id')
                ->with(['moons.achievements.requiredAchievements', 'achievements.requiredAchievements'])
                ->orderBy('name')
                ->get()
        );
    }
}
