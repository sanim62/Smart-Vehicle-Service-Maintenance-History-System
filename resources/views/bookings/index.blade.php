{{-- ================================================================ --}}
{{-- FILE: resources/views/bookings/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">All Bookings</h5>
    @if(auth()->user()->isOwner())
    <a href="{{ route('bookings.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Booking</a>
    @endif
</div>

{{-- Filter tabs --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('bookings.index') }}">All</a></li>
    @foreach(['pending','approved','in_progress','completed','cancelled'] as $s)
    <li class="nav-item"><a class="nav-link {{ request('status') === $s ? 'active' : '' }}" href="{{ route('bookings.index', ['status' => $s]) }}">{{ ucfirst(str_replace('_',' ',$s)) }}</a></li>
    @endforeach
</ul>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    @if(auth()->user()->isAdmin())<th>Customer</th>@endif
                    <th>Vehicle</th>
                    <th>Workshop</th>
                    <th>Date</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
            <tr>
                <td>{{ $booking->id }}</td>
                @if(auth()->user()->isAdmin())<td>{{ $booking->user->name ?? '—' }}</td>@endif
                <td>
                    <div>{{ $booking->vehicle->make ?? '—' }} {{ $booking->vehicle->model ?? '' }}</div>
                    <small class="text-muted">{{ $booking->vehicle->registration_number ?? '' }}</small>
                </td>
                <td>{{ $booking->workshop->name ?? '—' }}</td>
                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                <td>{{ $booking->service_type }}</td>
                <td>
                    <span class="badge badge-{{ $booking->status }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @if($booking->status === 'approved' && !$booking->service && (auth()->user()->isAdmin() || auth()->user()->isWorkshop()))
                        <a href="{{ route('services.create', ['booking_id' => $booking->id]) }}" class="btn btn-outline-success" title="Create Service Record">
                            <i class="bi bi-tools"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
