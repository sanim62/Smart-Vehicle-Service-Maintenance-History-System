@extends('layouts.app')
@section('title', 'Service History Log')
@section('page-title', 'Service History Log')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-funnel me-2"></i>Filter Service History</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('owner.history') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Vehicle</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->make }} {{ $v->model }} ({{ $v->registration_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Service Log History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Vehicle</th>
                        <th>Workshop</th>
                        <th>Service Type</th>
                        <th>Total Cost</th>
                        <th>Technician</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                    <tr>
                        <td><strong>{{ $s->service_date->format('d M Y') }}</strong></td>
                        <td>{{ $s->vehicle->make }} {{ $s->vehicle->model }}</td>
                        <td>{{ $s->workshop->name }}</td>
                        <td>{{ $s->booking->service_type ?? 'General Maintenance' }}</td>
                        <td class="fw-bold text-success">৳{{ number_format($s->total_cost) }}</td>
                        <td>{{ $s->technician_name ?? '—' }}</td>
                        <td>
                            @if($s->payment && $s->payment->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning"><i class="bi bi-hourglass-split me-1"></i>Unpaid</span>
                                <a href="{{ route('payments.checkout', $s) }}" class="btn btn-xs btn-sm btn-success text-white px-2 py-0 ms-2">Pay</a>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('services.show', $s) }}" class="btn btn-sm btn-outline-primary py-0"><i class="bi bi-eye me-1"></i>Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-tools text-muted opacity-25" style="font-size: 3rem;"></i>
                            <p class="mt-2 mb-0">No past services found matching your filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($services->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $services->links() }}
    </div>
    @endif
</div>
@endsection
