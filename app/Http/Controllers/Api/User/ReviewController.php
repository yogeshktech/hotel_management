<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\ReviewResource;
use App\Models\Booking;
use App\Models\BookingReview;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends ApiController
{
    #[OA\Get(path: '/user/reviews', tags: ['Reviews'], summary: 'My reviews', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Review list')]
    public function index(Request $request)
    {
        $reviews = $request->user()->reviews()
            ->with('homestay.location')
            ->latest()
            ->paginate(15);

        return $this->success([
            'items' => ReviewResource::collection($reviews),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    #[OA\Post(path: '/user/bookings/{id}/review', tags: ['Reviews'], summary: 'Submit review after checkout', security: [['sanctum' => []]])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['service_rating', 'food_rating', 'overall_rating'], properties: [
        new OA\Property(property: 'service_rating', type: 'integer', minimum: 1, maximum: 5),
        new OA\Property(property: 'food_rating', type: 'integer', minimum: 1, maximum: 5),
        new OA\Property(property: 'overall_rating', type: 'integer', minimum: 1, maximum: 5),
        new OA\Property(property: 'comment', type: 'string'),
    ]))]
    #[OA\Response(response: 201, description: 'Review created')]
    public function store(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== $request->user()->id) {
            return $this->error('Forbidden', 403);
        }

        if (! in_array($booking->status, ['checked_out', 'completed'])) {
            return $this->error('Review allowed only after checkout', 422);
        }

        if ($booking->review) {
            return $this->error('Already reviewed', 422);
        }

        $validated = $request->validate([
            'service_rating' => 'required|integer|min:1|max:5',
            'food_rating' => 'required|integer|min:1|max:5',
            'overall_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = BookingReview::create([
            ...$validated,
            'booking_id' => $booking->id,
            'customer_id' => $request->user()->id,
            'homestay_id' => $booking->homestay_id,
        ]);

        $review->load('homestay');

        return $this->success(new ReviewResource($review), 'Review submitted', 201);
    }
}
