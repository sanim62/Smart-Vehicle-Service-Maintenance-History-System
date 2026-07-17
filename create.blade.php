@extends('layouts.app')
@section('title', isset($part) ? 'Edit Part' : 'Add Part')
@section('page-title', isset($part) ? 'Edit Part' : 'Add Part')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-gear me-2"></i>
            {{ isset($part) ? 'Edit: ' . $part->name : 'Add New Part' }}
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($part) ? route('parts.update', $part) : route('parts.store') }}">
            @csrf
            @if(isset($part)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-medium">Part Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $part->name ?? '') }}" placeholder="e.g. Engine Oil Filter" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Part Number</label>
                <input type="text" name="part_number" class="form-control"
                       value="{{ old('part_number', $part->part_number ?? '') }}" placeholder="e.g. OEM-123456">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Category</label>
                <select name="category" class="form-select">
                    <option value="">— Select Category —</option>
                    @foreach(['Engine','Brakes','Electrical','Suspension','Transmission','Body','Cooling','Fuel System','Exhaust','Tyres','Filters','Fluids','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $part->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Unit Price (৳) <span class="text-danger">*</span></label>
                    <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                           step="0.01" min="0" value="{{ old('unit_price', $part->unit_price ?? '') }}" required>
                    @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Unit <span class="text-danger">*</span></label>
                    <select name="unit" class="form-select" required>
                        @foreach(['piece','liter','kg','set'] as $unit)
                        <option value="{{ $unit }}" {{ old('unit', $part->unit ?? 'piece') === $unit ? 'selected' : '' }}>
                            {{ ucfirst($unit) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>{{ isset($part) ? 'Update Part' : 'Add Part' }}
                </button>
                <a href="{{ route('parts.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
