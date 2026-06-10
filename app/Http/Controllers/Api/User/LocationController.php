<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\LocationResource;
use App\Models\Location;
use OpenApi\Attributes as OA;

class LocationController extends ApiController
{
    #[OA\Get(path: '/user/locations', tags: ['Locations'], summary: 'List all destinations')]
    #[OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'List of locations')]
    public function index()
    {
        $locations = Location::withCount(['homestays' => fn ($q) => $q->active()])
            ->when(request('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('city', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get();

        return $this->success(LocationResource::collection($locations));
    }

    #[OA\Get(path: '/user/locations/{id}', tags: ['Locations'], summary: 'Get location detail')]
    #[OA\Response(response: 200, description: 'Location detail')]
    public function show(Location $location)
    {
        $location->loadCount(['homestays' => fn ($q) => $q->active()]);

        return $this->success(new LocationResource($location));
    }
}
