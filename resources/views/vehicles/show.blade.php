@extends('layouts.app')
@section('title', $vehicle->make . ' ' . $vehicle->model)
@section('page-title', 'Vehicle Details')

@section('content')
<div class="row g-3">

    {{-- Vehicle Info Card --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $vehicle->make }} {{ $vehicle->model }}</h5>
                        <span class="text-muted">{{ $vehicle->year }}</span>
                    </div>
                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                </div>

                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Registration</td><td><code class="fw-bold">{{ $vehicle->registration_number }}</code></td></tr>
                    <tr><td class="text-muted">Chassis No.</td><td><small>{{ $vehicle->chassis_number }}</small></td></tr>
                    <tr><td class="text-muted">Color</td><td>{{ $vehicle->color ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Fuel Type</td><td>{{ ucfirst($vehicle->fuel_type) }}</td></tr>
                    <tr><td class="text-muted">Mileage</td><td>{{ number_format($vehicle->mileage) }} km</td></tr>
                    <tr><td class="text-muted">Owner</td><td>{{ $vehicle->user->name }}</td></tr>
                    <tr><td class="text-muted">Status</td>
                        <td><span class="badge {{ $vehicle->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($vehicle->status) }}</span></td>
                    </tr>
                </table>

                {{-- Maintenance Reminder --}}
                @php $ms = $vehicle->maintenance_status; @endphp
                <div class="alert {{ $ms === 'Normal' ? 'alert-success' : ($ms === 'Due Soon' ? 'alert-warning' : ($ms === 'Overdue' ? 'alert-danger' : 'alert-secondary')) }} py-2 mb-0">
                    <i class="bi bi-bell me-2"></i>
                    <strong>Maintenance: </strong>{{ $ms }}
                    @if($vehicle->last_service_date)
                        <div class="small mt-1">Last service: {{ \Carbon\Carbon::parse($vehicle->last_service_date)->format('d M Y') }}</div>
                    @endif
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="row text-center">
                    <div class="col">
                        <div class="fw-bold text-primary">{{ $services->count() }}</div>
                        <small class="text-muted">Services</small>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-success">৳{{ number_format($vehicle->total_spent) }}</div>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-grid gap-2">
            <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-primary">
                <i class="bi bi-calendar-plus me-1"></i> Book Service
            </a>
        </div>
    </div>

    {{-- Service History --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Service History</h6>
            </div>
            <div class="card-body p-0">
                @forelse($services as $service)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $service->service_date->format('d M Y') }}
                                <span class="badge bg-primary-subtle text-primary ms-2">{{ $service->workshop->name ?? '—' }}</span>
                            </div>
                            <div class="text-muted small mt-1">{{ Str::limit($service->issue_description, 100) }}</div>
                            @if($service->serviceParts->count())
                            <div class="mt-2">
                                @foreach($service->serviceParts as $sp)
                                <span class="badge bg-light text-dark border me-1">{{ $sp->part->name }} ×{{ $sp->quantity }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">৳{{ number_format($service->total_cost) }}</div>
                            <small class="text-muted">Labor: ৳{{ number_format($service->labor_cost) }}</small><br>
                            <small class="text-muted">Parts: ৳{{ number_format($service->parts_cost) }}</small>
                            <div class="mt-1">
                                <a href="{{ route('services.show', $service) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-tools" style="font-size:3rem;opacity:.2"></i>
                    <p class="mt-2">No service records yet.</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">Book a Service</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
