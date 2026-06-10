<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::table('roles')->update(['guard_name' => 'staff']);
        DB::table('permissions')->update(['guard_name' => 'staff']);

        $permissions = [
            'dashboard.view', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.manage', 'vendors.view', 'vendors.approve', 'vendors.manage',
            'properties.view', 'properties.approve', 'properties.manage',
            'bookings.view', 'bookings.manage', 'locations.manage',
            'promo-codes.manage', 'gift-cards.manage', 'banners.manage', 'blogs.manage', 'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'staff']);
        }

        $rolePermissions = [
            'super_admin' => Permission::where('guard_name', 'staff')->pluck('name')->toArray(),
            'admin_staff' => [
                'dashboard.view', 'users.view', 'vendors.view', 'vendors.approve',
                'properties.view', 'properties.approve', 'bookings.view', 'bookings.manage',
                'locations.manage', 'banners.manage', 'blogs.manage',
            ],
            'vendor' => ['dashboard.view', 'properties.view', 'properties.manage', 'bookings.view'],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'staff']);
            $role->syncPermissions($perms);
        }

        $superAdmin = Staff::firstOrCreate(
            ['email' => 'superadmin@hotel.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password123'), 'phone' => '+919876543210', 'role' => 'super_admin', 'department' => 'Management', 'is_active' => true]
        );
        $superAdmin->syncRoles('super_admin');

        Staff::firstOrCreate(
            ['email' => 'staff@hotel.com'],
            ['name' => 'Admin Staff', 'password' => bcrypt('password123'), 'phone' => '+919876543211', 'role' => 'admin_staff', 'department' => 'Operations', 'is_active' => true]
        )->syncRoles('admin_staff');
    }
}
