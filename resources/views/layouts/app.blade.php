<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <script src="{{ asset('js/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('js/job-modal.js') }}"></script>

    @livewireStyles
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }
        .sidebar {
            width: 250px;
            background-color: #212529;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link.active {
            background-color: #343a40;
            color: #fff;
        }
        .content {
            flex-grow: 1;
            /* padding: 20px; */
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    {{-- Sidebar --}}
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">{{ config('app.name', 'Job System') }}</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" 
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('jobs.deliver') }}" 
                class="nav-link {{ request()->routeIs('jobs.deliver') ? 'active' : '' }}">
                    Deliver Jobs
                </a>
            </li>
            <li>
                <a href="{{ route('jobs.received') }}" 
                class="nav-link {{ request()->routeIs('jobs.received') ? 'active' : '' }}">
                    Jobs Received
                </a>
            </li>
        </ul>

        <hr>
        <div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Logout</button>
            </form>
        </div>
    </div>

    {{-- Content --}}
<div class="content p-2">
    <nav class="navbar navbar-expand-lg" style="background: transparent; box-shadow: none;">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            {{-- Left side: breadcrumbs --}}
            <div class="d-flex align-items-center">
                <span class="me-2 text-muted">Pages /</span>
                <span class="fw-semibold">Dashboard</span>
                <!-- nanti bagian ini bisa dibuat dinamis pakai yield atau variable -->
            </div>

            {{-- Right side: profile dropdown --}}
            <ul class="navbar-nav">
                <li class="nav-item dropdown"> 
                    <a class="nav-link dropdown-toggle d-flex align-items-center border border-dark rounded py-2 px-3"
                       href="#"
                       id="userDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="fas fa-user-circle fa-xl me-2"></i>
                        <span>Username</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
                    </ul>
                </li>
            </ul>

        </div>
    </nav>

    @yield('content')
</div>


    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
</body>
</html>
