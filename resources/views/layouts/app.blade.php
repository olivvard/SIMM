<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Motor PM System') — Motor Preventive Maintenance</title>
    <meta name="description" content="Motor Preventive Maintenance Management System">

    <link rel="icon" type="image/x-icon" href="{{ asset('weai-ori.png') }}">

    {{-- Bootstrap 5.3 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav id="sidebar" class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon p-1">
            <a href="{{ route('dashboard') }}"><img src="{{ asset('weai-white.png') }}" alt="Logo" class="img-fluid"></a>
        </div>
        <div class="brand-text">
            <a href="{{ route('dashboard') }}" class="text-decoration-none"><span class="brand-title">WEAI-SIMM</span></a>
            <span class="brand-sub">Motor Preventif Maintenance</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-label">Main Menu</li>

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('motors.index') }}" class="nav-link {{ request()->routeIs('motors.*') ? 'active' : '' }}">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>Motors</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Schedules</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Maintenance Input</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Reports</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
            </div>
            <div class="user-details">
                <span class="user-name">{{ Auth::user()->full_name }}</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</nav>

{{-- Main Content --}}
<div id="main-content" class="main-content">
    {{-- Topbar --}}
    <header class="topbar">
        <button id="sidebar-toggle" class="sidebar-toggle-btn" type="button">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-breadcrumb">
            <h5 class="page-title mb-0">@yield('page-title', 'Dashboard')</h5>
        </div>

        <div class="topbar-right">
            <span class="topbar-date">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->format('d M Y') }}
            </span>
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="flash-container px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="page-content px-4 pb-5">
        @yield('content')
    </main>
</div>

{{-- Reverb Toast Container --}}
<div id="reverb-toasts" class="reverb-toast-container" aria-live="polite"></div>

{{-- Bootstrap 5.3 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Sidebar toggle --}}
<script>
    document.getElementById('sidebar-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('main-content').classList.toggle('expanded');
    });
</script>

@stack('scripts')
</body>
</html>
