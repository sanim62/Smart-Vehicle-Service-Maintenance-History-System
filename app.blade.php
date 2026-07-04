<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vehicle Service System')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <script>
        // Immediately apply theme to avoid flashing
        (function () {
            const getPreferredTheme = () => {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme) return storedTheme;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };
            document.documentElement.setAttribute('data-bs-theme', getPreferredTheme());
        })();
    </script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #1a56db;

            /* Light Theme (default) Colors */
            --body-bg: #f8fafc;
            --text-color: #1e293b;
            --topbar-bg: #ffffff;
            --topbar-border: #e2e8f0;
            --sidebar-bg: #1e293b;
            --sidebar-brand-color: #7dd3fc;
            --sidebar-text: #cbd5e1;
            --sidebar-active-bg: rgba(255, 255, 255, 0.08);
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --text-muted: #64748b;
        }

        [data-bs-theme="dark"] {
            /* Dark Theme Colors */
            --body-bg: #090d16;
            --text-color: #f1f5f9;
            --topbar-bg: #111827;
            --topbar-border: #1f2937;
            --sidebar-bg: #0d121f;
            --sidebar-brand-color: #38bdf8;
            --sidebar-text: #94a3b8;
            --sidebar-active-bg: rgba(255, 255, 255, 0.04);
            --card-bg: #111827;
            --card-border: #1f2937;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            --text-muted: #94a3b8;
        }

        body {
            background-image: linear-gradient(rgba(15, 23, 42, 0.82), rgba(15, 23, 42, 0.88)), url('{{ asset('images/background.jpg') }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 1.05rem; /* Increased from 1rem to 1.05rem for better readability */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: color 0.3s ease;
        }

        /* General legibility enhancements for key components */
        .table {
            font-size: 1.025rem; /* Larger table cells and headings */
        }
        .form-control, .form-select, .form-label, .btn, .input-group-text {
            font-size: 1.025rem; /* Clearer form fields, labels, and buttons */
        }
        .form-label {
            font-weight: 550; /* Slightly stronger weight for visual hierarchy */
        }
        .card-title, .card-header h5, .card-header h6 {
            font-size: 1.15rem; /* Distinct header size */
            font-weight: 600;
        }
        .badge {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.45em 0.75em;
        }
        .small, small {
            font-size: 0.875rem; /* Restrict small elements from being tiny */
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
            z-index: 100;
            transition: background-color 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand h5 { font-size: 1.2rem; font-weight: 700; margin: 0; color: var(--sidebar-brand-color); }
        .sidebar-brand small { color: var(--text-muted); font-size: .85rem; }
        .nav-section {
            padding: 1rem 1.25rem .35rem;
            font-size: .8rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: .08em;
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: .65rem 1.25rem; /* Slightly taller clickable area */
            border-radius: 0;
            font-size: .95rem; /* Increased from .875rem */
            display: flex; align-items: center; gap: .6rem;
            transition: all .15s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #fff;
        }
        .sidebar .nav-link.active { border-left: 3px solid #3b82f6; }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            padding: .875rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .topbar span.fw-semibold {
            font-size: 1.15rem; /* Slightly larger screen title */
        }
        .topbar .dropdown button {
            font-size: 0.95rem;
        }
        .page-body { padding: 1.5rem; }

        /* Cards */
        .stat-card {
            border: 1px solid var(--card-border) !important;
            border-radius: 12px;
            background: var(--card-bg) !important;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .card {
            border: 1px solid var(--card-border) !important;
            border-radius: 12px;
            background: var(--card-bg) !important;
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .card-header {
            background: var(--card-bg) !important;
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 1px solid var(--card-border) !important;
        }

        /* Dark mode safety overrides */
        [data-bs-theme="dark"] .text-dark {
            color: var(--text-color) !important;
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: var(--card-bg) !important;
        }
        [data-bs-theme="dark"] .border-bottom {
            border-bottom-color: var(--card-border) !important;
        }
        [data-bs-theme="dark"] .table {
            --bs-table-bg: var(--card-bg);
            --bs-table-border-color: var(--card-border);
            color: var(--text-color);
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #1f2937;
            border-color: #374151;
            color: var(--text-color);
        }
        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #1f2937;
            color: var(--text-color);
        }

        /* Badge status */
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-approved   { background: #dbeafe; color: #1e40af; }
        .badge-in_progress{ background: #ede9fe; color: #5b21b6; }
        .badge-completed  { background: #d1fae5; color: #065f46; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        [data-bs-theme="dark"] .badge-pending    { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        [data-bs-theme="dark"] .badge-approved   { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        [data-bs-theme="dark"] .badge-in_progress{ background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
        [data-bs-theme="dark"] .badge-completed  { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        [data-bs-theme="dark"] .badge-cancelled  { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        .maintenance-normal   { color: #059669; }
        .maintenance-due      { color: #d97706; }
        .maintenance-overdue  { color: #dc2626; }
        
        [data-bs-theme="dark"] .maintenance-normal   { color: #34d399; }
        [data-bs-theme="dark"] .maintenance-due      { color: #fbbf24; }
        [data-bs-theme="dark"] .maintenance-overdue  { color: #f87171; }
    </style>

    @stack('styles')
</head>
<body>

<!-- ─── Sidebar ─────────────────────────────────────────── -->
<nav class="sidebar">
    <a href="{{ url('/') }}" class="sidebar-brand d-block text-decoration-none">
        <h5><i class="bi bi-car-front-fill me-2"></i>VehicleServe</h5>
        <small>Service Management System</small>
    </a>

    <ul class="nav flex-column mt-2">
        <li class="nav-section">Main</li>
        <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a></li>

        @if(auth()->user()->isOwner())
            {{-- Owner Portal Navigation --}}
            <li class="nav-section">Vehicles</li>
            <li><a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                <i class="bi bi-car-front"></i> My Vehicles
            </a></li>

            <li class="nav-section">Workshops</li>
            <li><a href="{{ route('workshops.map') }}" class="nav-link {{ request()->routeIs('workshops.map') ? 'active' : '' }}">
                <i class="bi bi-map-fill"></i> Find Nearby Map
            </a></li>
            <li><a href="{{ route('workshops.index') }}" class="nav-link {{ request()->routeIs('workshops.index') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Browse Workshops
            </a></li>

            <li class="nav-section">Bookings & Services</li>
            <li><a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Bookings
            </a></li>
            <li><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Service History
            </a></li>
            <li><a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-back"></i> Payments & Receipts
            </a></li>

            <li class="nav-section">Reports</li>
            <li><a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Reports & Analytics
            </a></li>

            <li class="nav-section">Support</li>
            <li><a href="{{ route('complaints.create') }}" class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                <i class="bi bi-headset"></i> Complaints & Requests
            </a></li>

        @elseif(auth()->user()->isWorkshop())
            {{-- Workshop Portal Navigation --}}
            @php $myWorkshop = auth()->user()->workshops()->first(); @endphp
            <li class="nav-section">My Workshop</li>
            @if($myWorkshop)
                <li><a href="{{ route('workshops.show', $myWorkshop) }}" class="nav-link {{ request()->routeIs('workshops.show') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Workshop Profile
                </a></li>
                <li><a href="{{ route('workshops.edit', $myWorkshop) }}" class="nav-link {{ request()->routeIs('workshops.edit') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i> Configure Settings
                </a></li>
            @else
                <li><a href="{{ route('workshops.create') }}" class="nav-link {{ request()->routeIs('workshops.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-lg"></i> Register Workshop
                </a></li>
            @endif

            <li class="nav-section">Operations</li>
            <li><a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Customer Bookings
            </a></li>
            <li><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Repair Services
            </a></li>
            <li><a href="{{ route('parts.index') }}" class="nav-link {{ request()->routeIs('parts.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Parts Inventory
            </a></li>
            <li><a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Received Payouts
            </a></li>

            <li class="nav-section">Analytics</li>
            <li><a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Revenue Reports
            </a></li>

        @else
            {{-- Admin Navigation Fallback --}}
            <li class="nav-section">Workshops</li>
            <li><a href="{{ route('workshops.index') }}" class="nav-link {{ request()->routeIs('workshops.index') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Workshops List
            </a></li>
            <li><a href="{{ route('workshops.map') }}" class="nav-link {{ request()->routeIs('workshops.map') ? 'active' : '' }}">
                <i class="bi bi-map-fill"></i> Interactive Map
            </a></li>

            <li class="nav-section">Bookings & Services</li>
            <li><a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Bookings
            </a></li>
            <li><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Services
            </a></li>
            <li><a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-back"></i> Payments
            </a></li>
        @endif

        @if(auth()->user()->isAdmin())
        <li class="nav-section text-danger">Administration</li>
        <li><a href="{{ route('admin.dashboard') }}" class="nav-link text-danger-emphasis">
            <i class="bi bi-shield-lock-fill"></i> Admin Centre
        </a></li>
        @endif
    </ul>
</nav>

<!-- ─── Main Content ────────────────────────────────────── -->
<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div>
            <span class="fw-semibold">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; transition: transform 0.15s ease, background-color 0.15s ease;" title="Toggle Theme">
                <i id="theme-toggle-icon" class="bi bi-sun-fill fs-5"></i>
            </button>
            
            <span class="badge bg-primary-subtle text-primary px-2 py-1">
                {{ ucfirst(auth()->user()->role) }}
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="page-body pb-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <div class="page-body">
        @yield('content')
        {{ $slot ?? '' }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (!themeToggleBtn) return;
        const themeToggleIcon = document.getElementById('theme-toggle-icon');

        const updateToggleButton = (theme) => {
            if (theme === 'dark') {
                themeToggleIcon.className = 'bi bi-moon-stars-fill fs-5';
                themeToggleBtn.className = 'btn btn-outline-info btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center';
            } else {
                themeToggleIcon.className = 'bi bi-sun-fill fs-5';
                themeToggleBtn.className = 'btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center';
            }
        };

        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
        };

        // Initialize button state
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        updateToggleButton(currentTheme);

        themeToggleBtn.addEventListener('click', () => {
            const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
            
            // Micro-animation click feedback
            themeToggleBtn.style.transform = 'scale(0.85)';
            setTimeout(() => {
                themeToggleBtn.style.transform = 'scale(1)';
            }, 100);

            setTheme(newTheme);
            updateToggleButton(newTheme);
        });
    });
</script>
@stack('scripts')
</body>
</html>
