{{-- ================================================================ --}}
{{-- FILE: resources/views/services/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Services')
@section('page-title', 'Service Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Service History</h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Workshop</th>
                    <th>Service Date</th>
                    <th>Issue</th>
                    <th>Labor</th>
                    <th>Parts</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($services as $service)
            <tr>
                <td>#{{ $service->id }}</td>
                <td>
                    <div class="fw-semibold">{{ $service->vehicle->make ?? '—' }} {{ $service->vehicle->model ?? '' }}</div>
                    <small class="text-muted">{{ $service->vehicle->registration_number ?? '' }}</small>
                </td>
                <td>{{ $service->workshop->name ?? '—' }}</td>
                <td>{{ $service->service_date->format('d M Y') }}</td>
                <td><span title="{{ $service->issue_description }}">{{ Str::limit($service->issue_description, 40) }}</span></td>
                <td>৳{{ number_format($service->labor_cost) }}</td>
                <td>৳{{ number_format($service->parts_cost) }}</td>
                <td class="fw-bold text-success">৳{{ number_format($service->total_cost) }}</td>
                <td><a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No service records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($services->hasPages())
    <div class="card-footer">{{ $services->links() }}</div>
    @endif
</div>
@endsection
