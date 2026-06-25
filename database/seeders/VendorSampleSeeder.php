<?php

namespace Database\Seeders;

use App\Models\Homestay;
use App\Models\Location;
use App\Models\Room;
use App\Support\RoomPackagePricing;
use App\Models\Staff;
use App\Models\VendorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorSampleSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['slug' => 'manali', 'name' => 'Manali', 'city' => 'Manali', 'province' => 'Himachal Pradesh', 'lat' => 32.2432, 'lng' => 77.1892],
            ['slug' => 'goa', 'name' => 'Goa', 'city' => 'Calangute', 'province' => 'Goa', 'lat' => 15.5439, 'lng' => 73.7553],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(['slug' => $loc['slug']], [
                'name' => $loc['name'], 'country' => 'India', 'province' => $loc['province'],
                'city' => $loc['city'], 'latitude' => $loc['lat'], 'longitude' => $loc['lng'],
                'description' => "Popular destination — {$loc['name']}", 'homestays_count' => 0,
            ]);
        }

        $vendors = [
            ['name' => 'Rajesh Kumar', 'email' => 'vendor1@hotel.com', 'phone' => '+919811111111', 'business' => 'Himalayan Retreat Resort', 'loc' => 'manali', 'property' => 'Himalayan Retreat Villa', 'status' => 'approved'],
            ['name' => 'Priya Sharma', 'email' => 'vendor2@hotel.com', 'phone' => '+919822222222', 'business' => 'Valley View Homestay', 'loc' => 'manali', 'property' => 'Valley View Cottage', 'status' => 'pending'],
            ['name' => 'Anil D\'Souza', 'email' => 'vendor3@hotel.com', 'phone' => '+919833333333', 'business' => 'Beach Paradise Resort', 'loc' => 'goa', 'property' => 'Beach Paradise Suite', 'status' => 'approved'],
        ];

        foreach ($vendors as $v) {
            $staff = Staff::firstOrCreate(['email' => $v['email']], [
                'name' => $v['name'], 'password' => bcrypt('password123'), 'phone' => $v['phone'],
                'role' => 'vendor', 'is_active' => true,
            ]);
            $staff->syncRoles('vendor');

            VendorProfile::firstOrCreate(['staff_id' => $staff->id], [
                'business_name' => $v['business'], 'contact_phone' => $v['phone'], 'contact_email' => $v['email'],
                'address' => 'Main Road', 'city' => $v['loc'] === 'manali' ? 'Manali' : 'Goa',
                'state' => $v['loc'] === 'manali' ? 'Himachal Pradesh' : 'Goa', 'pincode' => '175131',
                'description' => 'Premium hospitality with modern amenities.', 'status' => $v['status'],
                'approved_at' => $v['status'] === 'approved' ? now() : null,
            ]);

            $location = Location::where('slug', $v['loc'])->first();
            $property = Homestay::firstOrCreate(['slug' => Str::slug($v['property'])], [
                'staff_id' => $staff->id, 'location_id' => $location->id, 'title' => $v['property'],
                'description' => 'Beautiful property with scenic views and excellent service.',
                'max_guests' => 6, 'bedrooms' => 3, 'beds' => 4, 'bathrooms' => 2,
                'price_per_night' => 4500, 'currency' => 'INR',
                'amenities' => ['wifi', 'parking', 'restaurant', 'room-service'],
                'address' => 'Main Road', 'latitude' => $location->latitude, 'longitude' => $location->longitude,
                'status' => $v['status'] === 'approved' ? 'active' : 'pending',
            ]);

            $rooms = [
                ['name' => 'Deluxe Room', 'type' => 'deluxe', 'capacity' => 2, 'units' => 5, 'base' => 3500],
                ['name' => 'Family Suite', 'type' => 'suite', 'capacity' => 5, 'units' => 3, 'base' => 6500],
            ];

            foreach ($rooms as $r) {
                $room = Room::firstOrCreate(
                    ['homestay_id' => $property->id, 'name' => $r['name']],
                    ['room_type' => $r['type'], 'capacity' => $r['capacity'], 'bed_count' => 2,
                     'price_per_night' => $r['base'], 'total_units' => $r['units'], 'status' => 'active']
                );
                RoomPackagePricing::syncForRoom($room, $r['base']);
            }
        }
    }
}
