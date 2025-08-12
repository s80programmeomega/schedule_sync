<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ScheduleSync | Smart Appointment Scheduling</title>

    <!-- Bootstrap 5 CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"> --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --secondary: #f3f4f6;
            --dark: #111827;
            --light: #ffffff;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--dark);
            background-color: #fafafa;
        }

        /* Navigation */
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nav-link {
            font-weight: 500;
        }

        /* Custom Button Styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Calendar Grid */
        .calendar-grid {
            background-color: var(--light);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .calendar-header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .calendar-day {
            min-height: 120px;
            border: 1px solid var(--gray-light);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .calendar-day:hover {
            background-color: var(--secondary);
        }

        .calendar-day.active {
            background-color: rgba(99, 102, 241, 0.1);
            border-color: var(--primary);
        }

        .calendar-day.disabled {
            background-color: #f9fafb;
            color: #d1d5db;
            cursor: not-allowed;
        }

        .day-number {
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Time Slots */
        .time-slot {
            display: block;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-slot:hover {
            border-color: var(--primary);
            background-color: rgba(99, 102, 241, 0.05);
        }

        .time-slot.selected {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Event Types */
        .event-type-card {
            border-radius: 10px;
            border: 1px solid var(--gray-light);
            transition: all 0.2s ease;
            cursor: pointer;
            overflow: hidden;
        }

        .event-type-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .event-type-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease forwards;
        }

        .animate-delay-1 {
            animation-delay: 0.1s;
        }

        .animate-delay-2 {
            animation-delay: 0.2s;
        }

        .animate-delay-3 {
            animation-delay: 0.3s;
        }

        /* Sidebar */
        .sidebar {
            border-right: 1px solid var(--gray-light);
            height: calc(100vh - 70px);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--gray);
            border-radius: 8px;
            margin-bottom: 0.25rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Custom Form Styling */
        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--gray-light);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }

        label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">ScheduleSync<span class="text-primary">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Event Types</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Scheduled Events</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=6366f1&color=fff"
                                class="rounded-circle me-1" width="28" height="28" alt="Profile">
                            John Doe
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 px-0 d-none d-lg-block">
                <div class="sidebar py-4 px-3">
                    <div class="mb-4">
                        <a href="#" class="sidebar-link active">
                            <i class="bi bi-grid-1x2"></i>
                            Dashboard
                        </a>
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-calendar-event"></i>
                            Event Types
                        </a>
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-calendar-check"></i>
                            Scheduled Events
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
                            <i class="bi bi-zoom"></i>
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

            <!-- Main Content -->
            <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
                        <p class="text-muted mb-0">Manage your availability and bookings</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus me-2"></i> Create Event Type
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm animate-fade-in">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-3 rounded-circle bg-primary bg-opacity-10">
                                        <i class="bi bi-calendar-check text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title h2 mb-0 fw-bold">24</h5>
                                        <p class="card-text text-muted mb-0">Upcoming Meetings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm animate-fade-in animate-delay-1">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-3 rounded-circle bg-success bg-opacity-10">
                                        <i class="bi bi-check-circle text-success fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title h2 mb-0 fw-bold">18</h5>
                                        <p class="card-text text-muted mb-0">Completed Meetings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm animate-fade-in animate-delay-2">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-3 rounded-circle bg-danger bg-opacity-10">
                                        <i class="bi bi-x-circle text-danger fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title h2 mb-0 fw-bold">3</h5>
                                        <p class="card-text text-muted mb-0">Cancelled Meetings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Types -->
                <div class="mb-5 animate-fade-in animate-delay-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h4 fw-bold mb-0">Your Event Types</h2>
                        <a href="#" class="text-decoration-none">View All</a>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="event-type-card h-100 bg-white">
                                <div class="p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="event-type-icon me-3">
                                            <i class="bi bi-chat-square-text"></i>
                                        </div>
                                        <h3 class="h5 mb-0 fw-semibold">15 Minute Meeting</h3>
                                    </div>
                                    <p class="text-muted mb-3">Quick sync to discuss important matters</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-clock me-2"></i>
                                        <span>15 min</span>
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-link-45deg me-2"></i>
                                        <span class="text-truncate">scheduleSync.com/john/quick-chat</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-top py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="eventActive1"
                                                checked>
                                            <label class="form-check-label" for="eventActive1">Active</label>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-pencil me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="event-type-card h-100 bg-white">
                                <div class="p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="event-type-icon me-3">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h3 class="h5 mb-0 fw-semibold">30 Minute Consultation</h3>
                                    </div>
                                    <p class="text-muted mb-3">In-depth discussion about projects and goals</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-clock me-2"></i>
                                        <span>30 min</span>
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-link-45deg me-2"></i>
                                        <span class="text-truncate">scheduleSync.com/john/consultation</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-top py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="eventActive2"
                                                checked>
                                            <label class="form-check-label" for="eventActive2">Active</label>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-pencil me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="event-type-card h-100 bg-white">
                                <div class="p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="event-type-icon me-3">
                                            <i class="bi bi-laptop"></i>
                                        </div>
                                        <h3 class="h5 mb-0 fw-semibold">60 Minute Workshop</h3>
                                    </div>
                                    <p class="text-muted mb-3">A collaborative session to work through challenges</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-clock me-2"></i>
                                        <span>60 min</span>
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-link-45deg me-2"></i>
                                        <span class="text-truncate">scheduleSync.com/john/workshop</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-top py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="eventActive3"
                                                checked>
                                            <label class="form-check-label" for="eventActive3">Active</label>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-pencil me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="bi bi-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Meetings -->
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h4 fw-bold mb-0">Upcoming Meetings</h2>
                        <div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary active">Day</button>
                                <button type="button" class="btn btn-sm btn-outline-primary">Week</button>
                                <button type="button" class="btn btn-sm btn-outline-primary">Month</button>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Attendee</th>
                                            <th>Event Type</th>
                                            <th>Date & Time</th>
                                            <th>Duration</th>
                                            <th class="pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=4f46e5&color=fff"
                                                        class="rounded-circle me-3" width="42" height="42"
                                                        alt="Sarah Johnson">
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">Sarah Johnson</h6>
                                                        <span class="text-muted small">sarah@example.com</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>30 Minute Consultation</td>
                                            <td>
                                                <div>Today, 2:30 PM</div>
                                                <span class="badge bg-warning text-dark">In 45 minutes</span>
                                            </td>
                                            <td>30 min</td>
                                            <td class="pe-4">
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-light me-2" title="Reschedule"><i
                                                            class="bi bi-calendar"></i></button>
                                                    <button class="btn btn-sm btn-light me-2" title="Cancel"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-clipboard me-2"></i>Copy Invite
                                                                    Link</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-envelope me-2"></i>Email
                                                                    Attendee</a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item text-danger" href="#"><i
                                                                        class="bi bi-trash me-2"></i>Cancel Meeting</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Michael+Chen&background=4f46e5&color=fff"
                                                        class="rounded-circle me-3" width="42" height="42"
                                                        alt="Michael Chen">
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">Michael Chen</h6>
                                                        <span class="text-muted small">michael@example.com</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>15 Minute Meeting</td>
                                            <td>
                                                <div>Today, 4:00 PM</div>
                                                <span class="badge bg-secondary text-white">In 2 hours</span>
                                            </td>
                                            <td>15 min</td>
                                            <td class="pe-4">
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-light me-2" title="Reschedule"><i
                                                            class="bi bi-calendar"></i></button>
                                                    <button class="btn btn-sm btn-light me-2" title="Cancel"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-clipboard me-2"></i>Copy Invite
                                                                    Link</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-envelope me-2"></i>Email
                                                                    Attendee</a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item text-danger" href="#"><i
                                                                        class="bi bi-trash me-2"></i>Cancel Meeting</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Alex+Rodriguez&background=4f46e5&color=fff"
                                                        class="rounded-circle me-3" width="42" height="42"
                                                        alt="Alex Rodriguez">
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">Alex Rodriguez</h6>
                                                        <span class="text-muted small">alex@example.com</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>60 Minute Workshop</td>
                                            <td>
                                                <div>Tomorrow, 10:00 AM</div>
                                                <span class="badge bg-secondary text-white">In 1 day</span>
                                            </td>
                                            <td>60 min</td>
                                            <td class="pe-4">
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-light me-2" title="Reschedule"><i
                                                            class="bi bi-calendar"></i></button>
                                                    <button class="btn btn-sm btn-light me-2" title="Cancel"><i
                                                            class="bi bi-x-lg"></i></button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-clipboard me-2"></i>Copy Invite
                                                                    Link</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="bi bi-envelope me-2"></i>Email
                                                                    Attendee</a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item text-danger" href="#"><i
                                                                        class="bi bi-trash me-2"></i>Cancel Meeting</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Preview -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm calendar-grid">
                            <div class="calendar-header">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 fw-semibold">September 2023</h5>
                                    <div>
                                        <button class="btn btn-sm btn-primary bg-opacity-75 border-0 me-2"><i
                                                class="bi bi-chevron-left"></i></button>
                                        <button class="btn btn-sm btn-primary bg-opacity-75 border-0"><i
                                                class="bi bi-chevron-right"></i></button>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col">Mon</div>
                                    <div class="col">Tue</div>
                                    <div class="col">Wed</div>
                                    <div class="col">Thu</div>
                                    <div class="col">Fri</div>
                                    <div class="col">Sat</div>
                                    <div class="col">Sun</div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div class="col calendar-day disabled">
                                        <div class="p-2">
                                            <div class="day-number mb-2">28</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day disabled">
                                        <div class="p-2">
                                            <div class="day-number mb-2">29</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day disabled">
                                        <div class="p-2">
                                            <div class="day-number mb-2">30</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day disabled">
                                        <div class="p-2">
                                            <div class="day-number mb-2">31</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">1</div>
                                            <div
                                                class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
                                                2 events</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">2</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">3</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-0">
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">4</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">5</div>
                                            <div
                                                class="small py-1 px-2 bg-success text-white rounded mb-1 text-truncate">
                                                1 event</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">6</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">7</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">8</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">9</div>
                                            <div
                                                class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
                                                3 events</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">10</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-0">
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">11</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">12</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day active">
                                        <div class="p-2">
                                            <div class="day-number mb-2">13</div>
                                            <div
                                                class="small py-1 px-2 bg-warning text-dark rounded mb-1 text-truncate">
                                                Today</div>
                                            <div
                                                class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
                                                2 events</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">14</div>
                                            <div
                                                class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
                                                1 event</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">15</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">16</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">17</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-0">
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">18</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">19</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">20</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">21</div>
                                            <div
                                                class="small py-1 px-2 bg-success text-white rounded mb-1 text-truncate">
                                                1 event</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">22</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">23</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">24</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-0">
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">25</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">26</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">27</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">28</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">29</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day">
                                        <div class="p-2">
                                            <div class="day-number mb-2">30</div>
                                        </div>
                                    </div>
                                    <div class="col calendar-day disabled">
                                        <div class="p-2">
                                            <div class="day-number mb-2">1</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header border-bottom bg-white py-3">
                                <h5 class="card-title mb-0 fw-semibold">Today's Schedule</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Sarah Johnson</h6>
                                        <p class="text-muted mb-0 small">30 Minute Consultation • 2:30 PM</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Michael Chen</h6>
                                        <p class="text-muted mb-0 small">15 Minute Meeting • 4:00 PM</p>
                                    </div>
                                </div>

                                <div class="border-top my-4 pt-4">
                                    <h6 class="fw-semibold mb-3">Available Time Slots</h6>

                                    <div class="mb-3">
                                        <a href="#" class="time-slot">10:00 AM - 10:30 AM</a>
                                        <a href="#" class="time-slot">11:30 AM - 12:00 PM</a>
                                        <a href="#" class="time-slot">1:00 PM - 1:30 PM</a>
                                        <a href="#" class="time-slot selected">2:30 PM - 3:00 PM</a>
                                        <a href="#" class="time-slot">4:30 PM - 5:00 PM</a>
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle me-2"></i> Add Event
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Type Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Create New Event Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="eventName"
                            placeholder="e.g., 30 Minute Meeting">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="eventDuration" class="form-label">Duration</label>
                            <select class="form-select" id="eventDuration">
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="eventLocation" class="form-label">Location</label>
                            <select class="form-select" id="eventLocation">
                                <option value="zoom">Zoom Meeting</option>
                                <option value="google">Google Meet</option>
                                <option value="phone">Phone Call</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"
                            placeholder="Brief description about this meeting type"></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Start Date">
                                <span class="input-group-text">to</span>
                                <input type="text" class="form-control" placeholder="End Date">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Availability</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="monday"
                                        checked>
                                    <label class="form-check-label" for="monday">
                                        Mon
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="tuesday"
                                        checked>
                                    <label class="form-check-label" for="tuesday">
                                        Tue
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="wednesday"
                                        checked>
                                    <label class="form-check-label" for="wednesday">
                                        Wed
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="thursday"
                                        checked>
                                    <label class="form-check-label" for="thursday">
                                        Thu
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="friday"
                                        checked>
                                    <label class="form-check-label" for="friday">
                                        Fri
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Time Slots</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="9:00 AM">
                                <span class="input-group-text">to</span>
                                <input type="text" class="form-control" placeholder="5:00 PM">
                            </div>
                            <button class="btn btn-sm btn-outline-primary">+ Add Another Time Range</button>
                        </div>

                        <div class="col-md-6">
                            <label for="bufferTime" class="form-label">Buffer Time</label>
                            <select class="form-select" id="bufferTime">
                                <option value="0">No buffer time</option>
                                <option value="5">5 minutes before</option>
                                <option value="10" selected>10 minutes before</option>
                                <option value="15">15 minutes before</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Advanced Settings</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="hideEventType">
                            <label class="form-check-label" for="hideEventType">Hide event type</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="requireConfirmation">
                            <label class="form-check-label" for="requireConfirmation">Require confirmation before
                                scheduling</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="limitEvents">
                            <label class="form-check-label" for="limitEvents">Limit the number of events per
                                day</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Create Event Type</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS with Popper -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>

    <script>
        // Show tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Time slot selection
        const timeSlots = document.querySelectorAll('.time-slot');
        timeSlots.forEach(slot => {
            slot.addEventListener('click', function(e) {
                e.preventDefault();
                timeSlots.forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Create event button - show modal
        document.querySelector('.btn-primary').addEventListener('click', function() {
            const createEventModal = new bootstrap.Modal(document.getElementById('createEventModal'));
            createEventModal.show();
        });

        // Calendar day hover effect
        const calendarDays = document.querySelectorAll('.calendar-day:not(.disabled)');
        calendarDays.forEach(day => {
            day.addEventListener('click', function() {
                calendarDays.forEach(d => d.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
