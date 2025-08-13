<div class="col-lg-2 px-0 d-none d-lg-block">
    <div class="sidebar py-4 px-3">
        <div class="mb-4">
            <a href="{{ route('dashboard.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>
            <a href="{{ route('event-types.index') }}"
                class="sidebar-link {{ request()->routeIs('event-types.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                Event Types
            </a>
            <a href="{{ route('bookings.scheduled') }}"
                class="sidebar-link {{ request()->routeIs('bookings.scheduled') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                Scheduled Events
            </a>
            <a href="{{ route('bookings.index') }}" class="sidebar-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                All Bookings
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-people"></i>
                Team
            </a>
        </div>

        <div class="mt-4">
            <h6 class="text-uppercase text-muted fs-7 fw-semibold px-3 mb-3">INTEGRATIONS</h6>
            <a href="#" class="sidebar-link">
                <i class="bi bi-google"></i>
                Google Calendar
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-microsoft"></i>
                Outlook
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-camera-video-fill"></i>
                Zoom
            </a>
        </div>

        <div class="mt-auto pt-5">
            <a href="#" class="sidebar-link">
                <i class="bi bi-gear"></i>
                Settings
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-question-circle"></i>
                Help & Support
            </a>
        </div>
    </div>
</div>
