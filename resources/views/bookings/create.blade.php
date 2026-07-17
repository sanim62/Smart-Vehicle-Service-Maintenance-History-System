@extends('layouts.app')
@section('title', 'New Booking')
@section('page-title', 'Book a Service')

@section('content')
<div class="row justify-content-center text-dark">
<div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-calendar-plus me-2"></i>Book a Service Appointment</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-medium text-dark">Select Vehicle <span class="text-danger">*</span></label>
                <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                    <option value="">— Choose your vehicle —</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', request('vehicle_id')) == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }}) — {{ $vehicle->registration_number }}
                    </option>
                    @endforeach
                </select>
                @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($vehicles->isEmpty())
                <div class="form-text text-danger">You have no active vehicles. <a href="{{ route('vehicles.create') }}">Add one first</a>.</div>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium text-dark">Select Workshop <span class="text-danger">*</span></label>
                <select name="workshop_id" id="workshop_id" class="form-select @error('workshop_id') is-invalid @enderror" required>
                    <option value="">— Choose a workshop —</option>
                    @foreach($workshops as $workshop)
                    <option value="{{ $workshop->id }}" {{ old('workshop_id', request('workshop_id')) == $workshop->id ? 'selected' : '' }}>
                        {{ $workshop->name }} — {{ $workshop->city }}
                    </option>
                    @endforeach
                </select>
                @error('workshop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Booking Date <span class="text-danger">*</span></label>
                    <input type="date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror"
                           value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}" required>
                    @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Preferred Time</label>
                    <input type="time" name="booking_time" class="form-control" value="{{ old('booking_time') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium text-dark">Service Type <span class="text-danger">*</span></label>
                <select name="service_type" id="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                    <option value="">— Select service type —</option>
                    @foreach(['Oil Change', 'Tire Service', 'Brake Service', 'Engine Repair', 'Electrical', 'AC Service', 'Body Work', 'Transmission', 'General Service', 'Vehicle Inspection', 'Other'] as $type)
                    <option value="{{ $type }}" {{ old('service_type', request('service_type')) === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Upfront Estimate Info Box --}}
            <div id="estimate-box" class="alert alert-success d-none mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-tag-fill me-3 fs-4 text-success"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-success">Upfront Pricing Estimate</h6>
                        <p class="mb-0 text-slate-700 small">
                            Estimated Cost: <strong id="est-price"></strong><br>
                            Estimated Duration: <strong id="est-duration"></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium text-dark">Problem Description</label>
                <textarea name="problem_description" class="form-control" rows="4"
                          placeholder="Describe the issue or what you need done...">{{ old('problem_description') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Confirm Booking</button>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const workshopSelect = document.getElementById('workshop_id');
    const serviceSelect = document.getElementById('service_type');
    const estimateBox = document.getElementById('estimate-box');
    const estPrice = document.getElementById('est-price');
    const estDuration = document.getElementById('est-duration');

    const categoryMap = {
        'Oil Change': 'oil_change',
        'Tire Service': 'tire_service',
        'Brake Service': 'brake_service',
        'Engine Repair': 'engine_repair',
        'Electrical': 'electrical',
        'AC Service': 'ac_service',
        'Body Work': 'body_work',
        'Transmission': 'transmission',
        'General Service': 'general_service',
        'Vehicle Inspection': 'inspection'
    };

    function fetchEstimate() {
        const workshopId = workshopSelect.value;
        const serviceName = serviceSelect.value;
        const categoryKey = categoryMap[serviceName];

        if (!workshopId || !categoryKey) {
            estimateBox.classList.add('d-none');
            return;
        }

        fetch(`{{ route('bookings.estimate') }}?workshop_id=${workshopId}&service_type=${categoryKey}`)
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    estPrice.innerText = data.price_range;
                    estDuration.innerText = `${data.duration_hours} hour(s)`;
                    estimateBox.classList.remove('d-none');
                } else {
                    estimateBox.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error('Error fetching service pricing estimate:', err);
                estimateBox.classList.add('d-none');
            });
    }

    workshopSelect.addEventListener('change', fetchEstimate);
    serviceSelect.addEventListener('change', fetchEstimate);

    // Initial check (in case parameters are prefilled)
    if (workshopSelect.value && serviceSelect.value) {
        fetchEstimate();
    }
});
</script>
@endpush
