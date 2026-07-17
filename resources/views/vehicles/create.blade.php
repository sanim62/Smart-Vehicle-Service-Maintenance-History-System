@extends('layouts.app')
@section('title', isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle')
@section('page-title', isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-car-front me-2"></i>
            {{ isset($vehicle) ? 'Edit Vehicle: ' . $vehicle->make . ' ' . $vehicle->model : 'Register New Vehicle' }}
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($vehicle) ? route('vehicles.update', $vehicle) : route('vehicles.store') }}">
            @csrf
            @if(isset($vehicle)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Make <span class="text-danger">*</span></label>
                    <input type="text" name="make" class="form-control @error('make') is-invalid @enderror"
                           value="{{ old('make', $vehicle->make ?? '') }}" placeholder="Toyota, Honda, BMW..." required>
                    @error('make')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Model <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                           value="{{ old('model', $vehicle->model ?? '') }}" placeholder="Corolla, Civic..." required>
                    @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                           value="{{ old('year', $vehicle->year ?? date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                    @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Color</label>
                    <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}" placeholder="White, Black...">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Fuel Type <span class="text-danger">*</span></label>
                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror" required>
                        @foreach(['petrol', 'diesel', 'electric', 'hybrid'] as $fuel)
                        <option value="{{ $fuel }}" {{ old('fuel_type', $vehicle->fuel_type ?? '') === $fuel ? 'selected' : '' }}>
                            {{ ucfirst($fuel) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Registration Number <span class="text-danger">*</span></label>
                    <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror"
                           value="{{ old('registration_number', $vehicle->registration_number ?? '') }}"
                           placeholder="DHA-1234" style="text-transform:uppercase" required>
                    @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Chassis Number <span class="text-danger">*</span></label>
                    <input type="text" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror"
                           value="{{ old('chassis_number', $vehicle->chassis_number ?? '') }}"
                           placeholder="Enter VIN / Chassis No." required>
                    @error('chassis_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Current Mileage (km)</label>
                    <input type="number" name="mileage" class="form-control" min="0"
                           value="{{ old('mileage', $vehicle->mileage ?? 0) }}">
                </div>

                @if(isset($vehicle))
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ $vehicle->status === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $vehicle->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>{{ isset($vehicle) ? 'Update Vehicle' : 'Add Vehicle' }}
                </button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
