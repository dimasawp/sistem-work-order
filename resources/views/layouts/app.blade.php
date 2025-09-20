<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>{{ config('app.name', 'Laravel App') }}</title> --}}
    <title>WorkOrder</title>

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
        {{-- <h4 class="text-white mb-4">{{ config('app.name', 'Job System') }}</h4> --}}
        <h4 class="text-white mb-4">Work Order</h4>
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
                    Buat Job
                </a>
            </li>
            @role('job-receiver')
                <li>
                    <a href="{{ route('jobs.received') }}"
                        class="nav-link {{ request()->routeIs('jobs.received') ? 'active' : '' }}">
                        Job Masuk
                    </a>
                </li>
                <li>
                    <a href="{{ route('jobs.history') }}"
                        class="nav-link {{ request()->routeIs('jobs.history') ? 'active' : '' }}">
                        Histori Job
                    </a>
                </li>
            @endrole
            <li>
                <a href="{{ route('employees') }}"
                    class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}">
                    Daftar Karyawan
                </a>
            </li>
        </ul>

        <hr>
        <div>
            <a class="btn btn-danger w-100" href="#" role="button" onclick="openLogoutModal()">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div class="content p-2">
        <nav class="navbar navbar-expand-lg" style="background: transparent; box-shadow: none;">
            <div class="container-fluid d-flex justify-content-between align-items-center">

                {{-- Left side: breadcrumbs --}}
                <div class="d-flex align-items-center">
                    <span class="me-2 text-muted">Pages /</span>
                    <span class="fw-semibold">@yield('page-name')</span>
                </div>

                {{-- Right side: profile dropdown --}}
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center border border-dark rounded py-2 px-3"
                            href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-user-circle fa-xl me-2"></i>
                            <span>{{ auth()->user()->username }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                            {{-- <li><a class="dropdown-item" href="#">Settings</a></li> --}}
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="openLogoutModal()">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </nav>

        @yield('content')

        <!-- Modal Konfirmasi Logout -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-sign-out-alt fa-2x text-danger mb-3 p-4 border border-4 border-danger rounded-circle"></i>
                        <p>Apakah kamu yakin ingin logout dari aplikasi?</p>
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function openLogoutModal() {
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show();
        }
    </script>
    @livewireScripts
</body>
</html>