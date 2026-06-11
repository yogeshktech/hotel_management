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
            'dashboard.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.manage', 'roles.delete',
            'vendors.view', 'vendors.approve', 'vendors.manage', 'vendors.delete',
            'properties.view', 'properties.approve', 'properties.manage', 'properties.delete',
            'bookings.view', 'bookings.manage', 'bookings.delete',
            'locations.manage', 'locations.delete',
            'documents.delete',
            'promo-codes.manage', 'promo-codes.delete',
            'gift-cards.manage', 'gift-cards.delete',
            'banners.manage', 'banners.delete',
            'blogs.manage', 'blogs.delete',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'staff']);
        }

        $rolePermissions = [
            'super_admin' => Permission::where('guard_name', 'staff')->pluck('name')->toArray(),
            'admin_staff' => [
                'dashboard.view',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view',
                'vendors.view', 'vendors.approve', 'vendors.manage', 'vendors.delete',
                'properties.view', 'properties.approve', 'properties.delete',
                'bookings.view', 'bookings.manage', 'bookings.delete',
                'locations.manage', 'locations.delete',
                'banners.manage', 'blogs.manage',
            ],
            'vendor' => [
                'dashboard.view',
                'properties.view', 'properties.manage', 'properties.delete',
                'bookings.view', 'bookings.manage', 'bookings.delete',
                'locations.manage', 'locations.delete',
                'documents.delete',
            ],
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
