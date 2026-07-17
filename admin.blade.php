<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — VehicleServe</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            --sidebar-w: 270px;
            --admin-dark: #0f172a;
            --admin-darker: #080d18;
            --admin-accent: #ef4444;
            --admin-accent-hover: #dc2626;
            --admin-border: rgba(255,255,255,0.07);
            --admin-text: #94a3b8;
            --admin-text-active: #f1f5f9;

            /* Theme Colors */
            --body-bg: #f1f5f9;
            --text-color: #1e293b;
            --topbar-bg: #ffffff;
            --topbar-border: #e2e8f0;
            --card-bg: #ffffff;
            --card-border: #f1f5f9;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }

        [data-bs-theme="dark"] {
            --body-bg: #090d16;
            --text-color: #f1f5f9;
            --topbar-bg: #111827;
            --topbar-border: #1f2937;
            --card-bg: #111827;
            --card-border: #1f2937;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            --admin-dark: #0d121f;
            --admin-darker: #060810;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            font-size: 1.025rem;
            background: var(--body-bg);
            color: var(--text-color);
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ── Admin Sidebar ─────────────────────────────── */
        .admin-sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--admin-dark);
            position: fixed;
            top: 0; left: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            border-right: 1px solid var(--admin-border);
            transition: background-color 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem 1.2rem;
            border-bottom: 1px solid var(--admin-border);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .brand-icon {
            width: 40px; height: 40px;
            background: var(--admin-accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(239,68,68,0.3);
        }

        .brand-text {
            line-height: 1.2;
        }

        .brand-text strong {
            display: block;
            color: #f1f5f9;
            font-size: 1rem;
            font-weight: 700;
        }

        .brand-text span {
            color: var(--admin-accent);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-admin-badge {
            margin: 1rem 1.5rem 0.5rem;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 8px;
            padding: 0.6rem 1rem;
        }

        .sidebar-admin-badge .admin-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .sidebar-admin-badge .admin-role {
            font-size: 0.72rem;
            color: var(--admin-accent);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0;
        }

        .nav-group-label {
            padding: 0.75rem 1.5rem 0.3rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            font-weight: 700;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 1.5rem;
            color: var(--admin-text);
            text-decoration: none;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 0.15s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .admin-nav-link i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        .admin-nav-link:hover {
            background: rgba(255,255,255,0.04);
            color: var(--admin-text-active);
        }

        .admin-nav-link.active {
            background: rgba(239,68,68,0.1);
            color: var(--admin-accent);
            border-left-color: var(--admin-accent);
        }

        .admin-nav-link .badge-count {
            margin-left: auto;
            background: var(--admin-accent);
            color: white;
            font-size: 0.68rem;
            padding: 0.2em 0.55em;
            border-radius: 20px;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--admin-border);
        }

        .sidebar-footer form button {
            width: 100%;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            border-radius: 8px;
            padding: 0.55rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sidebar-footer form button:hover {
            background: var(--admin-accent);
            border-color: var(--admin-accent);
            color: white;
        }

        /* ── Main Content ──────────────────────────────── */
        .admin-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            padding: 1rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .admin-topbar .page-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-topbar .admin-tag {
            background: rgba(239,68,68,0.1);
            color: var(--admin-accent);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2em 0.6em;
            border-radius: 4px;
            border: 1px solid rgba(239,68,68,0.2);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .admin-body {
            padding: 1.75rem;
            flex: 1;
        }

        /* ── Cards ─────────────────────────────────────── */
        .card {
            border: 1px solid var(--card-border) !important;
            border-radius: 14px;
            background: var(--card-bg) !important;
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .card-header {
            background: var(--card-bg) !important;
            border-radius: 14px 14px 0 0 !important;
            border-bottom: 1px solid var(--card-border) !important;
        }

        .stat-card {
            background: var(--card-bg) !important;
            border-radius: 14px;
            padding: 1.4rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--card-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }

        /* ── Tables ────────────────────────────────────── */
        .table { font-size: 0.95rem; }
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; }

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

        /* ── Status badges ─────────────────────────────── */
        .badge-open      { background: #fef3c7; color: #92400e; }
        .badge-in-review { background: #dbeafe; color: #1e40af; }
        .badge-resolved  { background: #d1fae5; color: #065f46; }
        .badge-closed    { background: #f1f5f9; color: #475569; }

        [data-bs-theme="dark"] .badge-open      { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        [data-bs-theme="dark"] .badge-in-review { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        [data-bs-theme="dark"] .badge-resolved  { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        [data-bs-theme="dark"] .badge-closed    { background: rgba(71, 85, 105, 0.15); color: #94a3b8; }

        /* ── Form & Button sizing ──────────────────────── */
        .form-control, .form-select, .form-label, .btn {
            font-size: 0.975rem;
        }

        /* ── Alert messages ────────────────────────────── */
        .alert { border-radius: 10px; }

        /* ── Scrollbar ─────────────────────────────────── */
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    </style>

    @stack('styles')
</head>
<body>

<!-- ── Admin Sidebar ─────────────────────────────────────────── -->
<nav class="admin-sidebar">
    <!-- Brand -->
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-fill-check"></i>
            </div>
            <div class="brand-text">
                <strong>VehicleServe</strong>
                <span>Control Centre</span>
            </div>
        </a>
    </div>

    <!-- Admin identity badge -->
    <div class="sidebar-admin-badge">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-badge-fill text-danger"></i>
            <div>
                <div class="admin-name">{{ auth()->user()->name }}</div>
                <div class="admin-role">System Administrator</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-nav">
        <div class="nav-group-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.financials') }}" class="admin-nav-link {{ request()->routeIs('admin.financials') ? 'active' : '' }}">
            <i class="bi bi-bank2"></i> Financial Commission
        </a>

        <div class="nav-group-label">User Management</div>
        <a href="{{ route('admin.users') }}" class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> All Users
        </a>

        <div class="nav-group-label">Support Inbox</div>
        <a href="{{ route('admin.complaints') }}" class="admin-nav-link {{ request()->routeIs('admin.complaints*') ? 'active' : '' }}">
            <i class="bi bi-inbox-fill"></i> Complaints & Requests
            @php $openCount = \App\Models\Complaint::where('status','open')->count(); @endphp
            @if($openCount > 0)
                <span class="badge-count">{{ $openCount }}</span>
            @endif
        </a>

        <div class="nav-group-label">System</div>
        <a href="{{ route('admin.auditLogs') }}" class="admin-nav-link {{ request()->routeIs('admin.auditLogs') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Audit Logs
        </a>
    </div>

    <!-- Logout -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                <i class="bi bi-box-arrow-left"></i> Sign Out
            </button>
        </form>
    </div>
</nav>

<!-- ── Main Content ──────────────────────────────────────────── -->
<div class="admin-main">
    <!-- Topbar -->
    <div class="admin-topbar">
        <div class="page-title">
            <span class="admin-tag"><i class="bi bi-shield-fill me-1"></i>Admin</span>
            @yield('page-title', 'Dashboard')
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; transition: transform 0.15s ease, background-color 0.15s ease;" title="Toggle Theme">
                <i id="theme-toggle-icon" class="bi bi-sun-fill fs-5"></i>
            </button>
            <span class="text-muted small">{{ now()->format('D, d M Y') }}</span>
        </div>
    </div>

    <!-- Flash messages -->
    <div class="admin-body pb-0">
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
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page content -->
    <div class="admin-body">
        @yield('content')
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
