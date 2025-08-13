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
                <!-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                        href="{{ route('dashboard.index') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('event-types.*') ? 'active' : '' }}"
                        href="{{ route('event-types.index') }}">Event Types</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bookings.scheduled') ? 'active' : '' }}"
                        href="{{ route('bookings.scheduled') }}">Scheduled</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}"
                        href="{{ route('bookings.index') }}">All Bookings</a>
                </li> -->

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=6366f1&color=fff"
                            class="rounded-circle me-1" width="28" height="28" alt="Profile" />
                    </a>
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
                            <a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a>
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
