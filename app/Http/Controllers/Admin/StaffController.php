<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->get('role');

        $staffMembers = Staff::with('roles')
            ->when($roleFilter, fn ($q) => $q->role($roleFilter))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::where('guard_name', 'staff')->orderBy('name')->get();

        return view('admin.staff.index', compact('staffMembers', 'roles', 'roleFilter'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'staff')->orderBy('name')->get();

        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $staff = Staff::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['roles'][0],
            'is_active' => $request->boolean('is_active', true),
        ]);

        $staff->syncRoles($validated['roles']);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created.');
    }

    public function edit(Staff $staff)
    {
        $roles = Role::where('guard_name', 'staff')->orderBy('name')->get();
        $permissions = Permission::where('guard_name', 'staff')->orderBy('name')->get();

        return view('admin.staff.edit', compact('staff', 'roles', 'permissions'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'role' => $validated['roles'][0],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['password'])) {
            $staff->update(['password' => Hash::make($validated['password'])]);
        }

        $staff->syncRoles($validated['roles']);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function updatePermissions(Request $request, Staff $staff)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $staff->syncPermissions($request->input('permissions', []));

        return redirect()->back()->with('success', 'Permissions updated.');
    }

    public function toggleActive(Staff $staff)
    {
        if ($staff->hasRole('super_admin') && $staff->id !== auth('staff')->id()) {
            return redirect()->back()->with('error', 'Cannot deactivate another Super Admin.');
        }

        $staff->update(['is_active' => ! $staff->is_active]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->hasRole('super_admin') || $staff->id === auth('staff')->id()) {
            return redirect()->back()->with('error', 'Cannot delete this account.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted.');
    }
}
