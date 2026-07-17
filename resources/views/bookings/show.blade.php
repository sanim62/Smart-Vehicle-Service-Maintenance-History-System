@extends('layouts.app')
@section('title', 'Booking #' . $booking->id)
@section('page-title', 'Booking Details')

@section('content')
<div class="row g-3">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Booking #{{ $booking->id }}</h6>
                <span class="badge badge-{{ $booking->status }} fs-6">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted" width="40%">Vehicle</td>
                        <td class="fw-semibold">{{ $booking->vehicle->make }} {{ $booking->vehicle->model }} ({{ $booking->vehicle->year }})</td></tr>
                    <tr><td class="text-muted">Reg No.</td><td><code>{{ $booking->vehicle->registration_number }}</code></td></tr>
                    <tr><td class="text-muted">Workshop</td><td>{{ $booking->workshop->name }}</td></tr>
                    <tr><td class="text-muted">Location</td><td>{{ $booking->workshop->city }}</td></tr>
                    <tr><td class="text-muted">Booking Date</td><td>{{ $booking->booking_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Time</td><td>{{ $booking->booking_time ?? 'Not specified' }}</td></tr>
                    <tr><td class="text-muted">Service Type</td><td>{{ $booking->service_type }}</td></tr>
                    <tr><td class="text-muted">Problem</td><td>{{ $booking->problem_description ?? '—' }}</td></tr>
                    @if($booking->notes)
                    <tr><td class="text-muted">Notes</td><td>{{ $booking->notes }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Service Record (if created) --}}
        @if($booking->service)
        <div class="card mt-3">
            <div class="card-header bg-success bg-opacity-10 py-3">
                <h6 class="mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Service Completed</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted">Service Date</td><td>{{ $booking->service->service_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Issue</td><td>{{ $booking->service->issue_description }}</td></tr>
                    <tr><td class="text-muted">Repair</td><td>{{ $booking->service->repair_details }}</td></tr>
                    <tr><td class="text-muted">Labor Cost</td><td>৳{{ number_format($booking->service->labor_cost) }}</td></tr>
                    <tr><td class="text-muted">Parts Cost</td><td>৳{{ number_format($booking->service->parts_cost) }}</td></tr>
                    <tr><td class="text-muted fw-bold">Total Cost</td><td class="fw-bold text-success fs-5">৳{{ number_format($booking->service->total_cost) }}</td></tr>
                </table>
                <a href="{{ route('services.show', $booking->service) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-eye me-1"></i>Full Service Details
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- Actions Panel --}}
    <div class="col-md-5">
        {{-- Status Update (Workshop/Admin only) --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isWorkshop())
        <div class="card mb-3">
            <div class="card-header bg-white py-3"><h6 class="mb-0">Update Status</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('bookings.updateStatus', $booking) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['pending','approved','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ',$s)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $booking->notes }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>

        {{-- Create Service Button --}}
        @if($booking->status === 'approved' && !$booking->service)
        <div class="d-grid">
            <a href="{{ route('services.create', ['booking_id' => $booking->id]) }}" class="btn btn-success btn-lg">
                <i class="bi bi-tools me-2"></i>Create Service Record
            </a>
        </div>
        @endif
        @endif

        {{-- Cancel button for owner --}}
        @if(auth()->user()->isOwner() && in_array($booking->status, ['pending','approved']))
        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger w-100 mt-2">
                <i class="bi bi-x-circle me-1"></i>Cancel Booking
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
