<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $staffUsers = [
            [
                'email' => 'superadmin@hotel.com',
                'name' => 'Super Admin',
                'phone' => '+919876543210',
                'role' => 'super_admin',
                'department' => 'Management',
                'spatie_role' => 'super_admin',
            ],
            [
                'email' => 'admin@hotel.com',
                'name' => 'Admin',
                'phone' => '+919876543211',
                'role' => 'admin_staff',
                'department' => 'Management',
                'spatie_role' => 'admin_staff',
            ],
            [
                'email' => 'staff@hotel.com',
                'name' => 'Staff',
                'phone' => '+919876543212',
                'role' => 'staff',
                'department' => 'Operations',
                'spatie_role' => 'staff',
            ],
        ];

        foreach ($staffUsers as $data) {
            $staff = Staff::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'department' => $data['department'],
                    'is_active' => true,
                ]
            );

            $staff->syncRoles($data['spatie_role']);
        }

        Customer::updateOrCreate(
            ['email' => 'user@customer.com'],
            [
                'name' => 'Demo User',
                'phone' => '+919900000099',
                'password' => $password,
                'city' => 'Delhi',
                'is_active' => true,
            ]
        );
    }
}
