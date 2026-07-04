@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

{{-- ── ADMIN DASHBOARD ────────────────────────────────── --}}
@if($user->isAdmin())
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-people-fill"></i></div>
                <div><div class="fs-4 fw-bold">{{ $data['totalUsers'] }}</div><div class="text-muted small">Users</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-car-front-fill"></i></div>
                <div><div class="fs-4 fw-bold">{{ $data['totalVehicles'] }}</div><div class="text-muted small">Vehicles</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-shop-window"></i></div>
                <div><div class="fs-4 fw-bold">{{ $data['totalWorkshops'] }}</div><div class="text-muted small">Workshops</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-purple-subtle" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-calendar-check-fill"></i></div>
                <div><div class="fs-4 fw-bold">{{ $data['totalBookings'] }}</div><div class="text-muted small">Bookings</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-tools"></i></div>
                <div><div class="fs-4 fw-bold">{{ $data['totalServices'] }}</div><div class="text-muted small">Services</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-currency-dollar"></i></div>
                <div><div class="fs-4 fw-bold">৳{{ number_format($data['totalRevenue']) }}</div><div class="text-muted small">Revenue</div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3"><h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>User</th><th>Vehicle</th><th>Workshop</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($data['recentBookings'] as $b)
                    <tr>
                        <td>{{ $b->user->name ?? '—' }}</td>
                        <td>{{ $b->vehicle->make ?? '—' }} {{ $b->vehicle->model ?? '' }}</td>
                        <td>{{ $b->workshop->name ?? '—' }}</td>
                        <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3"><h6 class="mb-0"><i class="bi bi-wrench-adjustable me-2"></i>Recent Services</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Vehicle</th><th>Workshop</th><th>Date</th><th>Cost</th></tr></thead>
                    <tbody>
                    @foreach($data['recentServices'] as $s)
                    <tr>
                        <td>{{ $s->vehicle->make ?? '—' }} {{ $s->vehicle->model ?? '' }}</td>
                        <td>{{ $s->workshop->name ?? '—' }}</td>
                        <td>{{ $s->service_date->format('d M Y') }}</td>
                        <td class="fw-semibold text-success">৳{{ number_format($s->total_cost) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── OWNER DASHBOARD ─────────────────────────────────── --}}
@elseif($user->isOwner())

{{-- Maintenance Alerts --}}
@if($data['overdueVehicles']->count() > 0)
<div class="alert alert-danger d-flex align-items-center mb-3">
    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
    <div><strong>{{ $data['overdueVehicles']->count() }} vehicle(s) overdue</strong> for maintenance!
    {{ $data['overdueVehicles']->pluck('registration_number')->join(', ') }}</div>
</div>
@endif
@if($data['dueSoonVehicles']->count() > 0)
<div class="alert alert-warning d-flex align-items-center mb-3">
    <i class="bi bi-bell-fill me-3 fs-4"></i>
    <div><strong>{{ $data['dueSoonVehicles']->count() }} vehicle(s)</strong> due for service soon.</div>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-car-front-fill"></i></div>
                <div><div class="fs-3 fw-bold">{{ $data['totalVehicles'] }}</div><div class="text-muted small">My Vehicles</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-calendar-check-fill"></i></div>
                <div><div class="fs-3 fw-bold">{{ $data['totalBookings'] }}</div><div class="text-muted small">Total Bookings</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-tools"></i></div>
                <div><div class="fs-3 fw-bold">{{ $data['totalServices'] }}</div><div class="text-muted small">Services Done</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-wallet2"></i></div>
                <div><div class="fs-3 fw-bold">৳{{ number_format($data['totalSpent']) }}</div><div class="text-muted small">Total Spent</div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>My Vehicles</h6>
                <a href="{{ route('vehicles.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Add</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Vehicle</th><th>Reg No.</th><th>Maintenance</th></tr></thead>
                    <tbody>
                    @foreach($data['vehicles'] as $v)
                    <tr>
                        <td><a href="{{ route('vehicles.show', $v) }}">{{ $v->make }} {{ $v->model }} ({{ $v->year }})</a></td>
                        <td><code>{{ $v->registration_number }}</code></td>
                        <td>
                            @php $ms = $v->maintenance_status; @endphp
                            <span class="fw-semibold {{ $ms === 'Normal' ? 'maintenance-normal' : ($ms === 'Due Soon' ? 'maintenance-due' : 'maintenance-overdue') }}">
                                <i class="bi bi-circle-fill" style="font-size:.5rem"></i> {{ $ms }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @if($data['vehicles']->isEmpty())
                    <tr><td colspan="3" class="text-center text-muted py-3">No vehicles yet. <a href="{{ route('vehicles.create') }}">Add one</a></td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Recent Bookings</h6>
                <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Book</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Vehicle</th><th>Workshop</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($data['recentBookings'] as $b)
                    <tr>
                        <td>{{ $b->vehicle->make ?? '—' }}</td>
                        <td>{{ $b->workshop->name ?? '—' }}</td>
                        <td>{{ $b->booking_date->format('d M Y') }}</td>
                        <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                    </tr>
                    @endforeach
                    @if($data['recentBookings']->isEmpty())
                    <tr><td colspan="4" class="text-center text-muted py-3">No bookings yet.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── WORKSHOP DASHBOARD ──────────────────────────────── --}}
@else
@if($data['activeWarnings']->count() > 0)
<div class="alert alert-danger d-flex align-items-center mb-4 border-danger">
    <i class="bi bi-shield-exclamation fs-3 me-3 text-danger"></i>
    <div>
        <strong>Attention Workshop Manager:</strong> You have received <strong>{{ $data['activeWarnings']->count() }} active compliance warning(s)</strong> from the platform authorities. Please review complaints immediately to avoid potential suspension of services.
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div><div class="fs-3 fw-bold text-dark">{{ $data['pendingBookings'] }}</div><div class="text-muted small">Pending Bookings</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-tools"></i></div>
                <div><div class="fs-3 fw-bold text-dark">{{ $data['totalServices'] }}</div><div class="text-muted small">Services Done</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-cash-stack"></i></div>
                <div><div class="fs-3 fw-bold text-dark">${{ number_format($data['totalRevenue'], 2) }}</div><div class="text-muted small">Revenue Earned</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card text-dark">
    <div class="card-header bg-white py-3"><h6 class="mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Pending Customer Bookings</h6></div>
    <div class="card-body p-0">
        <table class="table mb-0 text-dark align-middle">
            <thead class="table-light"><tr><th>Customer</th><th>Vehicle</th><th>Date</th><th>Service</th><th>Action</th></tr></thead>
            <tbody>
            @foreach($data['recentBookings'] as $b)
            <tr>
                <td>{{ $b->user->name ?? '—' }}</td>
                <td>{{ $b->vehicle->make ?? '—' }} {{ $b->vehicle->model ?? '' }}</td>
                <td>{{ $b->booking_date->format('d M Y') }}</td>
                <td>{{ $b->service_type }}</td>
                <td><a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">View Booking</a></td>
            </tr>
            @endforeach
            @if($data['recentBookings']->isEmpty())
            <tr><td colspan="5" class="text-center py-4 text-muted">No pending bookings received.</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
