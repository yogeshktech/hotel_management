<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\BookingResource;
use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\Room;
use App\Services\BookingPricingService;
use App\Services\RoomAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class BookingController extends ApiController
{
    #[OA\Post(path: '/user/bookings/calculate-price', tags: ['Bookings'], summary: 'Calculate booking price preview')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['room_id', 'guest_package', 'check_in', 'check_out'], properties: [
        new OA\Property(property: 'room_id', type: 'integer'),
        new OA\Property(property: 'guest_package', type: 'string', enum: ['adult', 'couple', 'family', 'child']),
        new OA\Property(property: 'child_count', type: 'integer', example: 0),
        new OA\Property(property: 'check_in', type: 'string', format: 'date'),
        new OA\Property(property: 'check_out', type: 'string', format: 'date'),
        new OA\Property(property: 'promo_code', type: 'string'),
    ]))]
    #[OA\Response(response: 200, description: 'Price breakdown')]
    public function calculatePrice(Request $request, BookingPricingService $pricingService, RoomAvailabilityService $availabilityService)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'promo_code' => 'nullable|string',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);

        try {
            $pricing = $pricingService->calculate(
                $room,
                $validated['guest_package'],
                $childCount,
                Carbon::parse($validated['check_in']),
                Carbon::parse($validated['check_out'])
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        $promoDiscount = 0;
        if (! empty($validated['promo_code'])) {
            $promo = $this->resolvePromo($validated['promo_code'], $pricing['total_price']);
            if ($promo) {
                $promoDiscount = $promo['discount'];
            }
        }

        $pricing['promo_discount'] = $promoDiscount;
        $pricing['total_price'] = max(0, $pricing['total_price'] - $promoDiscount);
        $pricing['available'] = $availabilityService->isAvailable(
            $room,
            $validated['check_in'],
            $validated['check_out']
        );
        $pricing['units_available'] = $availabilityService->availableUnits(
            $room,
            $validated['check_in'],
            $validated['check_out']
        );

        return $this->success($pricing);
    }

    #[OA\Get(path: '/user/bookings', tags: ['Bookings'], summary: 'My bookings list', security: [['sanctum' => []]])]
    #[OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Booking list')]
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()
            ->with(['homestay.location', 'room', 'review'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('booked_at')
            ->paginate($request->get('per_page', 15));

        return $this->success([
            'items' => BookingResource::collection($bookings),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    #[OA\Get(path: '/user/bookings/{id}', tags: ['Bookings'], summary: 'Booking detail & tracking', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Booking detail')]
    public function show(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== $request->user()->id) {
            return $this->error('Forbidden', 403);
        }

        $booking->load(['homestay.location', 'room', 'review']);

        return $this->success(new BookingResource($booking));
    }

    #[OA\Post(path: '/user/bookings', tags: ['Bookings'], summary: 'Create online booking', security: [['sanctum' => []]])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['room_id', 'guest_package', 'check_in', 'check_out'], properties: [
        new OA\Property(property: 'room_id', type: 'integer'),
        new OA\Property(property: 'guest_package', type: 'string', enum: ['adult', 'couple', 'family', 'child']),
        new OA\Property(property: 'child_count', type: 'integer'),
        new OA\Property(property: 'check_in', type: 'string', format: 'date'),
        new OA\Property(property: 'check_out', type: 'string', format: 'date'),
        new OA\Property(property: 'promo_code', type: 'string'),
        new OA\Property(property: 'guest_notes', type: 'string'),
        new OA\Property(property: 'payment_method', type: 'string', example: 'razorpay'),
    ]))]
    #[OA\Response(response: 201, description: 'Booking created')]
    public function store(Request $request, BookingPricingService $pricingService, RoomAvailabilityService $availabilityService)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'promo_code' => 'nullable|string',
            'guest_notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);

        if ($room->homestay->status !== 'active') {
            return $this->error('Property not available', 422);
        }

        if (! $availabilityService->isAvailable($room, $validated['check_in'], $validated['check_out'])) {
            return $this->error('Room not available for selected dates. It may be booked offline or online.', 422);
        }

        try {
            $pricing = $pricingService->calculate(
                $room,
                $validated['guest_package'],
                $childCount,
                Carbon::parse($validated['check_in']),
                Carbon::parse($validated['check_out'])
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        $promoDiscount = 0;
        $promoCode = null;
        if (! empty($validated['promo_code'])) {
            $promo = $this->resolvePromo($validated['promo_code'], $pricing['total_price']);
            if ($promo) {
                $promoDiscount = $promo['discount'];
                $promoCode = $validated['promo_code'];
                PromoCode::where('code', $promoCode)->first()?->increment('used_count');
            }
        }

        $total = max(0, $pricing['total_price'] - $promoDiscount);

        $booking = DB::transaction(function () use ($request, $room, $validated, $pricing, $promoDiscount, $promoCode, $total, $childCount) {
            return Booking::create([
                'homestay_id' => $room->homestay_id,
                'customer_id' => $request->user()->id,
                'room_id' => $room->id,
                'booking_channel' => 'online',
                'guest_package' => $validated['guest_package'],
                'adults_count' => $pricing['adults_count'],
                'children_count' => $pricing['children_count'],
                'guests' => $pricing['adults_count'] + $pricing['children_count'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'base_price' => $pricing['base_price'],
                'cleaning_fee' => $pricing['cleaning_fee'],
                'service_fee' => $pricing['service_fee'],
                'promo_discount' => $promoDiscount,
                'promo_code' => $promoCode,
                'total_price' => $total,
                'currency' => 'INR',
                'payment_method' => $validated['payment_method'] ?? 'razorpay',
                'payment_status' => 'pending',
                'status' => 'pending',
                'guest_notes' => $validated['guest_notes'] ?? null,
                'booked_at' => now(),
                'vacant_from' => $validated['check_out'],
            ]);
        });

        $booking->load(['homestay.location', 'room']);

        return $this->success(new BookingResource($booking), 'Booking created', 201);
    }

    #[OA\Post(path: '/user/bookings/{id}/cancel', tags: ['Bookings'], summary: 'Cancel booking', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Booking cancelled')]
    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== $request->user()->id) {
            return $this->error('Forbidden', 403);
        }

        if (in_array($booking->status, ['checked_in', 'checked_out', 'cancelled'])) {
            return $this->error('Cannot cancel this booking', 422);
        }

        $booking->update(['status' => 'cancelled']);

        return $this->success(new BookingResource($booking->fresh(['homestay', 'room'])), 'Booking cancelled');
    }

    private function resolvePromo(string $code, float $amount): ?array
    {
        $promo = PromoCode::where('code', $code)->where('active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (! $promo) {
            return null;
        }

        $discount = $promo->type === 'percentage'
            ? round($amount * ($promo->value / 100), 2)
            : (float) $promo->value;

        return ['discount' => $discount, 'promo' => $promo];
    }
}
