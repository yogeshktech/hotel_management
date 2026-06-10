<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'backend_staff',
            'field_staff',
            'user',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
            ], [
                'guard_name' => 'web',
            ]);
        }

        $users = [
            [
                'email' => 'backend@example.com',
                'name'  => 'Backend Staff',
                'role'  => 'backend_staff',
            ],
            [
                'email' => 'field@example.com',
                'name'  => 'Field Staff',
                'role'  => 'field_staff',
            ],
            [
                'email' => 'user@example.com',
                'name'  => 'Sample User',
                'role'  => 'user',
            ],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate([
                'email' => $u['email'],
            ], [
                'name'      => $u['name'],
                'password'  => bcrypt('password123'),
                'phone'     => '+628000000000',
                'role'      => $u['role'],
                'is_active' => true,
            ]);

            // Ensure the user has the role
            $user->syncRoles($u['role']);
        }
    }
}
