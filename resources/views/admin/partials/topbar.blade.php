<div class="admin-topbar">
    <div>
        <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">{{ now()->format('d M Y') }}</span>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:.85rem;">
                {{ strtoupper(substr(auth('staff')->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="fw-semibold small">{{ auth('staff')->user()->name }}</div>
                <div class="text-muted" style="font-size:.75rem;">
                    {{ auth('staff')->user()->roles->pluck('name')->join(', ') }}
                </div>
            </div>
        </div>
    </div>
</div>
