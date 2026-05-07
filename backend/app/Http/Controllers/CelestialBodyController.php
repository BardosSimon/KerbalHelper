<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCelestialBodyRequest;
use App\Http\Requests\UpdateCelestialBodyRequest;
use App\Http\Resources\CelestialBodyResource;
use App\Models\CelestialBody;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CelestialBodyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CelestialBodyResource::collection(
            CelestialBody::query()
                ->with(['parent', 'moons'])
                ->orderBy('body_type')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(StoreCelestialBodyRequest $request): CelestialBodyResource
    {
        return new CelestialBodyResource(CelestialBody::create($request->validated()));
    }

    public function show(CelestialBody $celestialBody): CelestialBodyResource
    {
        return new CelestialBodyResource(
            $celestialBody->load(['parent', 'moons', 'achievements.requiredAchievements'])
        );
    }

    public function update(UpdateCelestialBodyRequest $request, CelestialBody $celestialBody): CelestialBodyResource
    {
        $celestialBody->update($request->validated());

        return new CelestialBodyResource($celestialBody->refresh());
    }

    public function destroy(CelestialBody $celestialBody): Response
    {
        $celestialBody->delete();

        return response()->noContent();
    }
}
