@extends('layouts.app')
@section('title', 'Create Service Record')
@section('page-title', 'Create Service Record')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

@if($booking)
<div class="alert alert-info mb-3">
    <i class="bi bi-info-circle me-2"></i>
    Creating service record for: <strong>{{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</strong>
    ({{ $booking->vehicle->registration_number }}) at <strong>{{ $booking->workshop->name }}</strong>
</div>
@endif

<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-tools me-2"></i>Service Record</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('services.store') }}" id="serviceForm">
            @csrf

            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Service Date <span class="text-danger">*</span></label>
                    <input type="date" name="service_date" class="form-control @error('service_date') is-invalid @enderror"
                           value="{{ old('service_date', date('Y-m-d')) }}" required>
                    @error('service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Technician Name</label>
                    <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name') }}" placeholder="Name of technician">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mileage at Service (km)</label>
                    <input type="number" name="mileage_at_service" class="form-control" min="0" value="{{ old('mileage_at_service', $booking->vehicle->mileage ?? '') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Issue Description <span class="text-danger">*</span></label>
                <textarea name="issue_description" class="form-control @error('issue_description') is-invalid @enderror"
                          rows="3" placeholder="Describe the issue reported by customer..." required>{{ old('issue_description', $booking->problem_description ?? '') }}</textarea>
                @error('issue_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Repair Details <span class="text-danger">*</span></label>
                <textarea name="repair_details" class="form-control @error('repair_details') is-invalid @enderror"
                          rows="3" placeholder="Describe what was done to fix the issue..." required>{{ old('repair_details') }}</textarea>
                @error('repair_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Labor Cost (৳) <span class="text-danger">*</span></label>
                    <input type="number" name="labor_cost" id="laborCost" class="form-control @error('labor_cost') is-invalid @enderror"
                           step="0.01" min="0" value="{{ old('labor_cost', 0) }}" required>
                    @error('labor_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-control" value="{{ old('next_service_date') }}">
                    <div class="form-text">Set reminder for next maintenance</div>
                </div>
            </div>

            {{-- Parts Section --}}
            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-gear me-2"></i>Parts Used</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPartBtn">
                            <i class="bi bi-plus me-1"></i>Add Part
                        </button>
                    </div>

                    <div id="partsContainer">
                        {{-- Parts rows added dynamically --}}
                    </div>

                    <div class="text-end mt-3 pt-3 border-top">
                        <div class="text-muted small">Parts Cost: ৳<span id="totalPartsCost">0.00</span></div>
                        <div class="fw-bold">Estimated Total: ৳<span id="estimatedTotal">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-lg me-1"></i>Save Service Record
                </button>
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
const parts = @json($parts);
let partIndex = 0;

document.getElementById('addPartBtn').addEventListener('click', function() {
    const container = document.getElementById('partsContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 align-items-end part-row';
    row.dataset.index = partIndex;
    row.innerHTML = `
        <div class="col-md-4">
            <label class="form-label small">Part</label>
            <select name="parts[${partIndex}][part_id]" class="form-select form-select-sm part-select" required>
                <option value="">— Select Part —</option>
                ${parts.map(p => `<option value="${p.id}" data-price="${p.unit_price}">${p.name} (${p.category ?? 'General'})</option>`).join('')}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Qty</label>
            <input type="number" name="parts[${partIndex}][quantity]" class="form-control form-control-sm part-qty" min="1" value="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small">Unit Price (৳)</label>
            <input type="number" name="parts[${partIndex}][unit_price]" class="form-control form-control-sm part-price" step="0.01" min="0" value="0" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Total</label>
            <div class="form-control form-control-sm bg-white part-total">৳0.00</div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-part-btn"><i class="bi bi-trash"></i></button>
        </div>
    `;
    container.appendChild(row);

    // Auto-fill price when part selected
    row.querySelector('.part-select').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const price = selected.dataset.price || 0;
        row.querySelector('.part-price').value = price;
        updateRowTotal(row);
    });

    // Update total on qty/price change
    row.querySelector('.part-qty').addEventListener('input', () => updateRowTotal(row));
    row.querySelector('.part-price').addEventListener('input', () => updateRowTotal(row));

    // Remove part row
    row.querySelector('.remove-part-btn').addEventListener('click', function() {
        row.remove();
        updateGrandTotal();
    });

    partIndex++;
});

function updateRowTotal(row) {
    const qty   = parseFloat(row.querySelector('.part-qty').value) || 0;
    const price = parseFloat(row.querySelector('.part-price').value) || 0;
    const total = qty * price;
    row.querySelector('.part-total').textContent = '৳' + total.toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    const laborCost  = parseFloat(document.getElementById('laborCost').value) || 0;
    const partsTotals = [...document.querySelectorAll('.part-total')].map(el => parseFloat(el.textContent.replace('৳','')) || 0);
    const partsCost  = partsTotals.reduce((a,b) => a + b, 0);
    document.getElementById('totalPartsCost').textContent = partsCost.toFixed(2);
    document.getElementById('estimatedTotal').textContent = (laborCost + partsCost).toFixed(2);
}

document.getElementById('laborCost').addEventListener('input', updateGrandTotal);
</script>
@endpush
