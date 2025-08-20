<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ScheduleSync')</title>

    <!-- Bootstrap 5 CSS for email compatibility -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        /* Existing CSS variables for consistency */
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --secondary: #f3f4f6;
            --dark: #111827;
            --light: #ffffff;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
        }

        /* Email-specific overrides using your design system */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            color: var(--dark) !important;
            background-color: #fafafa !important;
            line-height: 1.6;
        }

        /* Use your existing button styles */
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            display: inline-block !important;
        }

        .btn-outline-primary {
            color: var(--primary) !important;
            border-color: var(--primary) !important;
            background-color: transparent !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            display: inline-block !important;
        }

        /* Match your card styling */
        .email-card {
            background-color: var(--light);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-light);
            overflow: hidden;
        }

        .email-header {
            background-color: var(--primary);
            color: white;
            padding: 2rem;
        }

        /* Match your event-type-card styling */
        .meeting-details-card {
            background-color: var(--secondary);
            border-radius: 10px;
            border: 1px solid var(--gray-light);
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        /* Match your existing icon styling */
        .email-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0 10px !important;
            }

            .email-header,
            .p-4 {
                padding: 1.5rem !important;
            }

            .btn-primary,
            .btn-outline-primary {
                display: block !important;
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
        }
    </style>
</head>

<body>
    <div class="container" style="max-width: 600px; margin: 2rem auto;">
        <div class="email-card">
            <div class="email-header text-center">
                <h1 class="mb-2" style="font-weight: 700; letter-spacing: -0.5px;">
                    <i class="bi bi-calendar-check me-2"></i>ScheduleSync
                </h1>
                @yield('header-content')
            </div>

            <div class="p-4">
                @yield('content')
            </div>

            <div class="bg-light p-4 text-center border-top">
                <p class="text-muted mb-2">
                    <small>This email was sent by ScheduleSync</small>
                </p>
                <p class="mb-0">
                    <a href="{{ config('app.url') }}" class="text-decoration-none" style="color: var(--primary);">
                        Visit ScheduleSync
                    </a>
                    <span class="text-muted mx-2">|</span>
                    <a href="#" class="text-decoration-none" style="color: var(--primary);">
                        Unsubscribe
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
