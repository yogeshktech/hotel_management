<div class="vendor-topbar">
    <div>
        <h5 class="mb-0 fw-semibold">@yield('page-title', 'Vendor Dashboard')</h5>
        <small class="text-muted">{{ auth('staff')->user()->vendorProfile?->business_name ?? auth('staff')->user()->name }}</small>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge {{ auth('staff')->user()->vendorProfile?->status === 'approved' ? 'text-bg-success' : 'text-bg-warning' }}">
            {{ ucfirst(auth('staff')->user()->vendorProfile?->status ?? 'pending') }}
        </span>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                {{ auth('staff')->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('vendor.profile.edit') }}">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('staff.logout') }}"
                       onclick="event.preventDefault(); document.getElementById('vendor-logout').submit();">Logout</a>
                    <form id="vendor-logout" action="{{ route('staff.logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
            </ul>
        </div>
    </div>
</div>
