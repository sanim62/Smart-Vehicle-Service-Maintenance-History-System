@extends('layouts.app')
@section('title', isset($workshop) ? 'Edit Workshop' : 'Register Workshop')
@section('page-title', isset($workshop) ? 'Edit Workshop' : 'Register Workshop')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card text-dark">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="bi bi-shop me-2"></i>
            {{ isset($workshop) ? 'Edit: ' . $workshop->name : 'Register New Workshop' }}
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($workshop) ? route('workshops.update', $workshop) : route('workshops.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($workshop)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Workshop Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $workshop->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Owner Name <span class="text-danger">*</span></label>
                    <input type="text" name="owner_name" class="form-control @error('owner_name') is-invalid @enderror"
                           value="{{ old('owner_name', $workshop->owner_name ?? '') }}" required>
                    @error('owner_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $workshop->phone ?? '') }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $workshop->email ?? '') }}">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $workshop->address ?? '') }}" required>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           value="{{ old('city', $workshop->city ?? '') }}" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Map Coordinates --}}
                <div class="col-md-6">
                    <label class="form-label fw-medium">Latitude <span class="text-muted small">(for map finder)</span></label>
                    <input type="number" step="0.0000001" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
                           value="{{ old('latitude', $workshop->latitude ?? '23.8103') }}" placeholder="e.g. 23.8103">
                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Longitude <span class="text-muted small">(for map finder)</span></label>
                    <input type="number" step="0.0000001" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
                           value="{{ old('longitude', $workshop->longitude ?? '90.4125') }}" placeholder="e.g. 90.4125">
                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">License Number</label>
                    <input type="text" name="license_number" class="form-control"
                           value="{{ old('license_number', $workshop->license_number ?? '') }}">
                </div>

                @if(isset($workshop))
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ ($workshop->status ?? '') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($workshop->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ ($workshop->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                @endif

                <div class="col-12">
                    <label class="form-label fw-medium">Service Categories <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        @php
                            $selected = old('service_categories',
                                isset($workshop) ? (json_decode($workshop->service_categories, true) ?? []) : []
                            );
                        @endphp
                        @foreach($categories as $key => $label)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="service_categories[]"
                                       value="{{ $key }}" id="cat_{{ $key }}"
                                       {{ in_array($key, $selected) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('service_categories')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Brief description of the workshop...">{{ old('description', $workshop->description ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Workshop Photos <span class="text-muted small">(Optional, multiple allowed)</span></label>
                    <input type="file" name="photos[]" class="form-control @error('photos.*') is-invalid @enderror" multiple accept="image/*">
                    @error('photos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>{{ isset($workshop) ? 'Update Workshop' : 'Register Workshop' }}
                </button>
                <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
