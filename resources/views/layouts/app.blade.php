<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'Dashboard') | Sistem Evaluasi KP - UNSADA</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('images/unsada-logo.png') }}" type="image/png">
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <!-- [Material Icons] -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    @auth
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand text-primary text-center" style="display: block;">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <img src="{{ asset('images/unsada-logo.png') }}" alt="Universitas Darma Persada" style="max-width: 45px; height: auto; margin-right: 8px;">
                        <span class="fw-bold fs-5">UNSADA</span>
                    </div>
                    <div style="font-size: 0.7rem; line-height: 1.2; color: #6c757d; font-weight: 500;">
                        Sistem Evaluasi KP
                    </div>
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    @if(auth()->user()->isAdminOrKaprodi())
                        <li class="pc-item">
                            <a href="{{ route('admin.dashboard') }}" class="pc-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>
                    @elseif(auth()->user()->isDosen())
                        <li class="pc-item">
                            <a href="{{ route('dosen.dashboard') }}" class="pc-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>
                    @elseif(auth()->user()->isPembimbingLapangan())
                        <li class="pc-item">
                            <a href="{{ route('pembimbing-lapangan.dashboard') }}" class="pc-link {{ request()->routeIs('pembimbing-lapangan.dashboard') ? 'active' : '' }}">
                                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>
                    @elseif(auth()->user()->isMahasiswa())
                        <li class="pc-item">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="pc-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>
                    @endif

                    <li class="pc-item pc-caption">
                        <label>Evaluasi</label>
                        <i class="ti ti-clipboard"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('evaluations.index') }}" class="pc-link {{ request()->routeIs('evaluations.index') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-list"></i></span>
                            <span class="pc-mtext">Daftar Evaluasi</span>
                        </a>
                    </li>
                    @if(auth()->user()->isPembimbingLapangan())
                    <li class="pc-item">
                        <a href="{{ route('evaluations.create') }}" class="pc-link {{ request()->routeIs('evaluations.create') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-plus"></i></span>
                            <span class="pc-mtext">Buat Evaluasi</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdminOrKaprodi() || auth()->user()->isDosen())
                    <li class="pc-item pc-caption">
                        <label>Mahasiswa</label>
                        <i class="ti ti-users"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('students.index') }}" class="pc-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-school"></i></span>
                            <span class="pc-mtext">
                                @if(auth()->user()->isDosen())
                                    Mahasiswa Bimbingan
                                @else
                                    Daftar Mahasiswa
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isAdminOrKaprodi())
                    <li class="pc-item pc-caption">
                        <label>Manajemen</label>
                        <i class="ti ti-clipboard-list"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.users.index') }}" class="pc-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Kelola User</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.students.index') }}" class="pc-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-school"></i></span>
                            <span class="pc-mtext">Kelola Mahasiswa</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.companies.index') }}" class="pc-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-building"></i></span>
                            <span class="pc-mtext">Kelola Perusahaan</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Pengaturan</label>
                        <i class="ti ti-settings"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.settings.index') }}" class="pc-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-adjustments"></i></span>
                            <span class="pc-mtext">Application Settings</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper">
            <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->

            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                            <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar">
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                                        <span>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="pc-head-link bg-transparent border-0" title="Logout">
                                            <i class="ti ti-power text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="dropdown-body">
                                <!-- <a href="#" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>My Profile</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-settings"></i>
                                    <span>Settings</span>
                                </a> -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="ti ti-power"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p class="m-0">Sistem Evaluasi KP &copy; {{ date('Y') }} - Universitas Darma Persada</p>
                </div>
                <div class="col-auto my-1">
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-inline-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    @endauth

    @guest
    <div class="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                @yield('content')
            </div>
        </div>
    </div>
    @endguest

    <!-- Required Js -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    @stack('scripts')

    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>
</body>
</html>
