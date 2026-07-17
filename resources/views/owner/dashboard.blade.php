@extends('layouts.app')
@section('title', 'Service Seeker Dashboard')
@section('page-title', 'Service Seeker Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Maintenance Alerts --}}
@if($data['overdueVehicles']->count() > 0)
<div class="alert alert-danger d-flex align-items-center mb-3">
    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
    <div>
        <strong>{{ $data['overdueVehicles']->count() }} vehicle(s) overdue</strong> for maintenance!
        {{ $data['overdueVehicles']->pluck('registration_number')->join(', ') }}
    </div>
</div>
@endif

@if($data['dueSoonVehicles']->count() > 0)
<div class="alert alert-warning d-flex align-items-center mb-3">
    <i class="bi bi-bell-fill me-3 fs-4"></i>
    <div>
        <strong>{{ $data['dueSoonVehicles']->count() }} vehicle(s)</strong> due for service soon.
    </div>
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

<div class="row g-3 mb-4">
    {{-- Quick Discovery Cards --}}
    <div class="col-md-12">
        <div class="card p-3" style="background: linear-gradient(135deg, #1e293b, #0f172a); border: none;">
            <div class="row align-items-center">
                <div class="col-md-8 text-white">
                    <h4 class="fw-bold mb-2">Need a workshop nearby?</h4>
                    <p class="text-slate-300 mb-0">Search available workshops on our interactive map, check slot availability, get price estimates and book instantly.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('workshops.map') }}" class="btn btn-primary btn-lg"><i class="bi bi-map-fill me-2"></i>Find Workshop Map</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-car-front me-2"></i>My Registered Vehicles</h6>
                <a href="{{ route('vehicles.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Vehicle</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Vehicle</th>
                            <th>Reg No.</th>
                            <th>Maintenance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['vehicles'] as $v)
                        <tr>
                            <td>
                                <a href="{{ route('vehicles.show', $v) }}" class="fw-semibold text-decoration-none">
                                    {{ $v->make }} {{ $v->model }} ({{ $v->year }})
                                </a>
                            </td>
                            <td><code>{{ $v->registration_number }}</code></td>
                            <td>
                                @php $ms = $v->maintenance_status; @endphp
                                <span class="fw-semibold {{ $ms === 'Normal' ? 'maintenance-normal' : ($ms === 'Due Soon' ? 'maintenance-due' : 'maintenance-overdue') }}">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i> {{ $ms }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No vehicles registered yet. <a href="{{ route('vehicles.create') }}">Add your first vehicle</a> to request bookings.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-calendar-check me-2"></i>Recent Service Bookings</h6>
                <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-calendar-plus me-1"></i> Book Service</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Vehicle</th>
                            <th>Workshop</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recentBookings'] as $b)
                        <tr>
                            <td>{{ $b->vehicle->make ?? '—' }}</td>
                            <td>
                                <a href="{{ route('workshops.show', $b->workshop_id) }}" class="text-decoration-none">
                                    {{ $b->workshop->name ?? '—' }}
                                </a>
                            </td>
                            <td>{{ $b->booking_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $b->status }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No recent bookings.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
