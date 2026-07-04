@extends('layouts.app')
@section('title', 'Edit Service')
@section('page-title', 'Edit Service Record')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Service #{{ $service->id }}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('services.update', $service) }}">
            @csrf @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Service Date <span class="text-danger">*</span></label>
                    <input type="date" name="service_date" class="form-control"
                           value="{{ old('service_date', $service->service_date->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Technician Name</label>
                    <input type="text" name="technician_name" class="form-control"
                           value="{{ old('technician_name', $service->technician_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mileage at Service</label>
                    <input type="number" name="mileage_at_service" class="form-control" min="0"
                           value="{{ old('mileage_at_service', $service->mileage_at_service) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Issue Description <span class="text-danger">*</span></label>
                <textarea name="issue_description" class="form-control" rows="3" required>{{ old('issue_description', $service->issue_description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Repair Details <span class="text-danger">*</span></label>
                <textarea name="repair_details" class="form-control" rows="3" required>{{ old('repair_details', $service->repair_details) }}</textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Labor Cost (৳) <span class="text-danger">*</span></label>
                    <input type="number" name="labor_cost" class="form-control" step="0.01" min="0"
                           value="{{ old('labor_cost', $service->labor_cost) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-control"
                           value="{{ old('next_service_date', $service->next_service_date?->format('Y-m-d')) }}">
                </div>
            </div>

            {{-- Existing Parts Summary --}}
            @if($service->serviceParts->count())
            <div class="alert alert-info">
                <strong>Parts already added:</strong>
                @foreach($service->serviceParts as $sp)
                <span class="badge bg-light text-dark border ms-1">{{ $sp->part->name }} ×{{ $sp->quantity }}</span>
                @endforeach
                <div class="small mt-1">To manage parts, go back to the <a href="{{ route('services.show', $service) }}">service detail page</a>.</div>
            </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Service</button>
                <a href="{{ route('services.show', $service) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
