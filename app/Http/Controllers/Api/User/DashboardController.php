<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\BookingResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardController extends ApiController
{
    #[OA\Get(path: '/user/dashboard', tags: ['Dashboard'], summary: 'Dashboard stats & recent bookings', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Dashboard data')]
    public function index(Request $request)
    {
        $customer = $request->user();

        $bookings = $customer->bookings()
            ->with(['homestay.location', 'room', 'review'])
            ->latest('booked_at')
            ->take(5)
            ->get();

        return $this->success([
            'stats' => [
                'total_bookings' => $customer->bookings()->count(),
                'upcoming' => $customer->bookings()->where('check_in', '>=', now())->count(),
                'completed' => $customer->bookings()->where('status', 'checked_out')->count(),
                'reviews_given' => $customer->reviews()->count(),
            ],
            'recent_bookings' => BookingResource::collection($bookings),
        ]);
    }
}
