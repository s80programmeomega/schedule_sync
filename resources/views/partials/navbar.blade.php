<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">ScheduleSync<span class="text-primary">.</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        @auth
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- PLANNING Section -->
                <li class="nav-item d-lg-none"><hr class="dropdown-divider my-2"></li>
                <li class="nav-item d-lg-none">
                    <small class="text-muted text-uppercase fw-semibold px-3">PLANNING</small>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="sidebar-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                        href="{{ route('dashboard.index') }}">
                        <i class="bi bi-grid-1x2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="sidebar-link {{ request()->routeIs('event-types.*') ? 'active' : '' }}"
                        href="{{ route('event-types.index') }}">
                        <i class="bi bi-calendar-event"></i>
                        Event Types
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="sidebar-link {{ request()->routeIs('bookings.scheduled') ? 'active' : '' }}"
                        href="{{ route('bookings.scheduled') }}">
                        <i class="bi bi-calendar-check"></i>
                        Scheduled Events
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="sidebar-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}"
                        href="{{ route('bookings.index') }}">
                        <i class="bi bi-calendar3"></i>
                        All Bookings
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a class="sidebar-link {{ request()->routeIs('availability.*') ? 'active' : '' }}"
                        href="{{ route('availability.index') }}">
                        <i class="bi bi-clock"></i>
                        Availability
                    </a>
                </li>
                <!-- TEAM Section -->
                <li class="nav-item d-lg-none"><hr class="dropdown-divider my-2"></li>
                <li class="nav-item d-lg-none">
                    <small class="text-muted text-uppercase fw-semibold px-3">TEAM</small>
                </li>
                <li class="nav-item d-lg-none">
                    <a href="{{ route('teams.index') }}"
                        class="sidebar-link {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        Teams
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a href="{{ route('contacts.index') }}"
                        class="sidebar-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i>
                        Contacts
                    </a>
                </li>
                <li class="nav-item d-lg-none">
                    <a href="{{ route('groups.index') }}"
                        class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                        <i class="bi bi-collection"></i>
                        Groups
                    </a>
                </li>

                <li class="nav-item dropdown">
                    @if(auth()->user()->avatar)
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Current avatar" class="rounded-circle" width="28"
                            height="28">
                    </a>
                    @else
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=6366f1&color=fff"
                            class="rounded-circle me-1" width="28" height="28" alt="Profile" />
                    </a>
                    @endif
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header fw-bold">
                                {{ auth()->user()->name }}
                            </h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        @else
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @unless(request()->routeIs('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                @endunless @unless(request()->routeIs('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>
                @endunless
            </ul>
        </div>
        @endauth
    </div>
</nav>
