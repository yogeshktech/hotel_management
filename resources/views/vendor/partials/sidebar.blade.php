<aside class="vendor-sidebar">
    <div class="brand">
        {{ config('app.name', 'Homestay Booking') }}
        <small>Vendor Panel</small>
    </div>
    <nav class="vendor-nav">
        <div class="nav-section">Main</div>
        <a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <span>📊</span> Dashboard
        </a>

        <div class="nav-section">Onboarding</div>
        <a href="{{ route('vendor.profile.edit') }}" class="{{ request()->routeIs('vendor.profile.*') ? 'active' : '' }}">
            <span>👤</span> My Profile
        </a>
        <a href="{{ route('vendor.documents.index') }}" class="{{ request()->routeIs('vendor.documents.*') ? 'active' : '' }}">
            <span>📄</span> Documents
        </a>

        <div class="nav-section">Properties</div>
        <a href="{{ route('vendor.locations.index') }}" class="{{ request()->routeIs('vendor.locations.*') ? 'active' : '' }}">
            <span>📍</span> Locations
        </a>
        <a href="{{ route('vendor.properties.index') }}" class="{{ request()->routeIs('vendor.properties.*') && !request()->routeIs('vendor.rooms.*') ? 'active' : '' }}">
            <span>🏨</span> My Properties
        </a>

        <div class="nav-section">Operations</div>
        <a href="{{ route('vendor.bookings.index') }}" class="{{ request()->routeIs('vendor.bookings.index') || request()->routeIs('vendor.bookings.show') ? 'active' : '' }}">
            <span>📅</span> Bookings
        </a>
        <a href="{{ route('vendor.bookings.create-offline') }}" class="{{ request()->routeIs('vendor.bookings.create-offline') || request()->routeIs('vendor.bookings.store-offline') ? 'active' : '' }}">
            <span>🏨</span> Offline Booking
        </a>
    </nav>
</aside>
