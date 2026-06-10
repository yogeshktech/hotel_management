<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\Customer;
use App\Models\Homestay;
use App\Models\Room;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Amit Verma', 'email' => 'amit@customer.com', 'phone' => '+919900000001', 'city' => 'Delhi'],
            ['name' => 'Sneha Patel', 'email' => 'sneha@customer.com', 'phone' => '+919900000002', 'city' => 'Mumbai'],
            ['name' => 'Rahul Singh', 'email' => 'rahul@customer.com', 'phone' => '+919900000003', 'city' => 'Jaipur'],
            ['name' => 'Kavita Nair', 'email' => 'kavita@customer.com', 'phone' => '+919900000004', 'city' => 'Bangalore'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['email' => $c['email']], [
                'name' => $c['name'], 'phone' => $c['phone'], 'password' => bcrypt('password123'),
                'city' => $c['city'], 'is_active' => true,
            ]);
        }

        $staff = Staff::where('email', 'staff@hotel.com')->first();
        $properties = Homestay::active()->with('rooms')->get();

        if ($properties->isEmpty()) {
            return;
        }

        $bookingsData = [
            ['customer' => 'amit@customer.com', 'package' => 'couple', 'children' => 0, 'channel' => 'online', 'status' => 'checked_out', 'days_ago' => 10, 'nights' => 3],
            ['customer' => 'sneha@customer.com', 'package' => 'family', 'children' => 2, 'channel' => 'online', 'status' => 'checked_in', 'days_ago' => -1, 'nights' => 4],
            ['customer' => 'rahul@customer.com', 'package' => 'adult', 'children' => 0, 'channel' => 'offline', 'status' => 'confirmed', 'days_ago' => -5, 'nights' => 2],
            ['customer' => 'kavita@customer.com', 'package' => 'family', 'children' => 1, 'channel' => 'online', 'status' => 'confirmed', 'days_ago' => -10, 'nights' => 5],
            ['customer' => 'amit@customer.com', 'package' => 'child', 'children' => 2, 'channel' => 'offline', 'status' => 'pending', 'days_ago' => -15, 'nights' => 2],
        ];

        foreach ($bookingsData as $i => $bd) {
            $customer = Customer::where('email', $bd['customer'])->first();
            $property = $properties[$i % $properties->count()];
            $room = $property->rooms->first();
            if (! $room) {
                continue;
            }

            $checkIn = Carbon::now()->addDays($bd['days_ago']);
            $checkOut = (clone $checkIn)->addDays($bd['nights']);

            $pricing = $room->pricings()
                ->where('package_type', $bd['package'])
                ->where('child_count', in_array($bd['package'], ['family', 'child']) ? $bd['children'] : 0)
                ->first();

            $pricePerNight = $pricing?->price_per_night ?? $room->price_per_night;
            $total = $pricePerNight * $bd['nights'];

            $booking = Booking::create([
                'homestay_id' => $property->id,
                'customer_id' => $customer->id,
                'room_id' => $room->id,
                'booking_channel' => $bd['channel'],
                'guest_package' => $bd['package'],
                'adults_count' => $bd['package'] === 'couple' ? 2 : ($bd['package'] === 'child' ? 0 : 1),
                'children_count' => $bd['children'],
                'guests' => ($bd['package'] === 'couple' ? 2 : 1) + $bd['children'],
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'base_price' => $total,
                'cleaning_fee' => 500,
                'service_fee' => round($total * 0.1, 2),
                'total_price' => $total + 500 + round($total * 0.1, 2),
                'currency' => 'INR',
                'payment_status' => 'paid',
                'status' => $bd['status'],
                'booked_at' => (clone $checkIn)->subDays(7),
                'vacant_from' => $checkOut,
                'checked_in_at' => in_array($bd['status'], ['checked_in', 'checked_out']) ? $checkIn : null,
                'checked_out_at' => $bd['status'] === 'checked_out' ? $checkOut : null,
                'created_by_staff_id' => $bd['channel'] === 'offline' ? $staff?->id : null,
            ]);

            if ($bd['status'] === 'checked_out') {
                BookingReview::firstOrCreate(['booking_id' => $booking->id], [
                    'customer_id' => $customer->id,
                    'homestay_id' => $property->id,
                    'service_rating' => rand(3, 5),
                    'food_rating' => rand(3, 5),
                    'overall_rating' => rand(4, 5),
                    'comment' => 'Great stay! Service was excellent and food was delicious.',
                ]);
            }
        }
    }
}
