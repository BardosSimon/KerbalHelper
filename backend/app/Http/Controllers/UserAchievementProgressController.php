<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserAchievementProgressRequest;
use App\Http\Requests\UpdateUserAchievementProgressRequest;
use App\Http\Resources\UserAchievementProgressResource;
use App\Models\User;
use App\Models\UserAchievementProgress;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserAchievementProgressController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserAchievementProgressResource::collection(
            UserAchievementProgress::query()
                ->with('achievement.celestialBody')
                ->orderByDesc('completed_at')
                ->get()
        );
    }

    public function store(StoreUserAchievementProgressRequest $request): UserAchievementProgressResource
    {
        $validated = $request->validated();

        if (($validated['is_completed'] ?? false) && empty($validated['completed_at'])) {
            $validated['completed_at'] = now();
        }

        $progress = UserAchievementProgress::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'program_achievement_id' => $validated['program_achievement_id'],
            ],
            $validated
        );

        return new UserAchievementProgressResource($progress->load('achievement.celestialBody'));
    }

    public function show(UserAchievementProgress $progress): UserAchievementProgressResource
    {
        return new UserAchievementProgressResource($progress->load('achievement.celestialBody'));
    }

    public function update(UpdateUserAchievementProgressRequest $request, UserAchievementProgress $progress): UserAchievementProgressResource
    {
        $validated = $request->validated();

        if (($validated['is_completed'] ?? false) && empty($validated['completed_at']) && $progress->completed_at === null) {
            $validated['completed_at'] = now();
        }

        $progress->update($validated);

        return new UserAchievementProgressResource($progress->load('achievement.celestialBody'));
    }

    public function destroy(UserAchievementProgress $progress): Response
    {
        $progress->delete();

        return response()->noContent();
    }

    public function forUser(User $user): AnonymousResourceCollection
    {
        return UserAchievementProgressResource::collection(
            UserAchievementProgress::query()
                ->where('user_id', $user->id)
                ->with('achievement.celestialBody')
                ->orderByDesc('completed_at')
                ->get()
        );
    }
}
