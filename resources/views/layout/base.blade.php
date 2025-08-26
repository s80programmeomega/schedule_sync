<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>
    @yield('title', 'ScheduleSync | Smart Appointment Scheduling')
  </title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <!-- Custom CSS -->
  @vite(['resources/css/custom.css'])

  <!-- AOS Animation CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- AOS Animation JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

</head>

<body class="d-flex flex-column h-100">
  <script>
    AOS.init();
  </script>
  <header>
    <!-- Navbar -->
    @include('partials.navbar')
  </header>
  <main class="flex-shrink-0">
    <!-- Sidebar Toggle Button -->
    {{-- @auth
        <button class="btn btn-outline-primary btn-sm m-2 d-none d-md-inline-block" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    @endauth --}}
    <div class="container-fluid">

      <div class="row">
        <!-- Sidebar -->
        @auth
          @include('partials.sidebar')
        @endauth

        <!-- Main Content -->
        @yield('content')
      </div>
    </div>

    @auth
      <!-- Event Type Modal -->
      @include('partials.event_type_modal')
    @endauth
  </main>

  @include('partials.footer')

  <!-- Bootstrap 5 JS with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

  @vite(['resources/js/custom.js'])


</body>

</html>
