<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Peminjaman Fasilitas') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #6c757d;
        }
        .navbar-brand {
            font-weight: normal;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .btn {
            border-radius: 5px;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .nav-link.active {
            background-color: #495057 !important;
        }
    </style>
</head>
<body>
    @auth
    <div class="container-fluid">
        <div class="row">

            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h6 class="text-white">Peminjaman Fasilitas</h6>
                        <small class="text-white-50">Telkom University</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('facilities.*') ? 'active' : '' }}" 
                               href="{{ route('facilities.index') }}">
                                Fasilitas
                            </a>
                        </li>
                        
                        @if(auth()->user()->isMahasiswa())
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('bookings.*') ? 'active' : '' }}" 
                               href="{{ route('bookings.index') }}">
                                Peminjaman Saya
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isPenanggungjawab())
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('pj.approvals.*') ? 'active' : '' }}" 
                               href="{{ route('pj.approvals.index') }}">
                                Persetujuan
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}" 
                               href="{{ route('admin.approvals.index') }}">
                                Verifikasi Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" 
                               href="{{ route('admin.payments.index') }}">
                                Pembayaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('areas.*') ? 'active' : '' }}" 
                               href="{{ route('areas.index') }}">
                                Area
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('facility-types.*') ? 'active' : '' }}" 
                               href="{{ route('facility-types.index') }}">
                                Jenis Fasilitas
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <hr class="text-white-50">
                    
                    <div class="text-white p-2">
                        <div><strong>{{ auth()->user()->name }}</strong></div>
                        <small class="text-white-50">{{ auth()->user()->email }}</small>
                        <div class="mt-2">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-light">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h3>@yield('title', 'Dashboard')</h3>
                    @yield('actions')
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @else
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="/">
                Peminjaman Fasilitas - Telkom University
            </a>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html> 