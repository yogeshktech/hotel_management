<aside class="admin-sidebar">
    <div class="brand">
        Hotel Manager
        <small>Super Admin Panel</small>
    </div>
    <nav class="admin-nav">
        <div class="nav-section">Main</div>
        @can('dashboard.view')
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span>📊</span> Dashboard
        </a>
        @endcan

        <div class="nav-section">User Management</div>
        @can('users.view')
        <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <span>👥</span> Team & Staff
        </a>
        @endcan
        @can('customers.view')
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <span>🧳</span> Customers / Guests
        </a>
        @endcan
        <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
            <span>👤</span> My Profile
        </a>
        @can('roles.view')
        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <span>🔐</span> Roles & Permissions
        </a>
        @endcan

        <div class="nav-section">Vendor & Properties</div>
        @can('vendors.view')
        <a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
            <span>🏢</span> Vendors
            @php $pendingVendors = \App\Models\VendorProfile::pending()->count(); @endphp
            @if($pendingVendors > 0)
                <span class="badge bg-warning text-dark ms-auto">{{ $pendingVendors }}</span>
            @endif
        </a>
        @endcan
        @can('properties.view')
        <a href="{{ route('admin.properties.index') }}" class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
            <span>🏨</span> Properties
            @php $pendingProps = \App\Models\Homestay::pending()->count(); @endphp
            @if($pendingProps > 0)
                <span class="badge bg-warning text-dark ms-auto">{{ $pendingProps }}</span>
            @endif
        </a>
        @endcan

        <div class="nav-section">Operations</div>
        @can('bookings.view')
        <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <span>📅</span> Bookings
        </a>
        @endcan
        @can('locations.manage')
        <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
            <span>📍</span> Locations
        </a>
        @endcan

        <div class="nav-section">System</div>
        <a href="{{ url('/') }}" target="_blank"><span>🌐</span> View Website</a>
        <form action="{{ route('staff.logout') }}" method="POST" id="logout-form">@csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span>🚪</span> Logout</a>
        </form>
    </nav>
</aside>
