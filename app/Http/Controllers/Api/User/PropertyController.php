<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\PropertyResource;
use App\Http\Resources\User\RoomResource;
use App\Models\Homestay;
use OpenApi\Attributes as OA;

class PropertyController extends ApiController
{
    #[OA\Get(path: '/user/properties', tags: ['Properties'], summary: 'Search active properties')]
    #[OA\Parameter(name: 'location_id', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'min_price', in: 'query', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'max_price', in: 'query', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Response(response: 200, description: 'Paginated properties')]
    public function index()
    {
        $properties = Homestay::active()
            ->with(['location', 'images'])
            ->withAvg('reviews', 'overall_rating')
            ->when(request('location_id'), fn ($q, $id) => $q->where('location_id', $id))
            ->when(request('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('address', 'like', "%{$s}%"))
            ->when(request('min_price'), fn ($q, $p) => $q->where('price_per_night', '>=', $p))
            ->when(request('max_price'), fn ($q, $p) => $q->where('price_per_night', '<=', $p))
            ->latest()
            ->paginate(request('per_page', 15));

        return $this->success([
            'items' => PropertyResource::collection($properties),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    #[OA\Get(path: '/user/properties/{id}', tags: ['Properties'], summary: 'Property detail with rooms & pricing')]
    #[OA\Response(response: 200, description: 'Property detail')]
    public function show(Homestay $property)
    {
        if ($property->status !== 'active') {
            return $this->error('Property not available', 404);
        }

        $property->load(['location', 'images', 'rooms.pricings', 'rooms.images']);
        $property->loadAvg('reviews', 'overall_rating');
        $property->increment('view_count');

        return $this->success(new PropertyResource($property));
    }

    #[OA\Get(path: '/user/properties/{id}/rooms', tags: ['Properties'], summary: 'List rooms with package pricing')]
    #[OA\Response(response: 200, description: 'Room list with pricing')]
    public function rooms(Homestay $property)
    {
        if ($property->status !== 'active') {
            return $this->error('Property not available', 404);
        }

        $rooms = $property->rooms()->with(['pricings', 'images'])->where('status', 'active')->get();

        return $this->success(RoomResource::collection($rooms));
    }
}
