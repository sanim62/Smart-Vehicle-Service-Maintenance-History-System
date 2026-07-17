@extends('layouts.app')
@section('title', 'Service #' . $service->id)
@section('page-title', 'Service Details')

@section('content')
<div class="row g-3">
    <div class="col-md-8">
        {{-- Main Service Info --}}
        <div class="card mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-tools me-2"></i>Service Record #{{ $service->id }}</h6>
                <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Vehicle</label>
                        <div class="fw-semibold">{{ $service->vehicle->make }} {{ $service->vehicle->model }} ({{ $service->vehicle->year }})</div>
                        <code>{{ $service->vehicle->registration_number }}</code>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Workshop</label>
                        <div class="fw-semibold">{{ $service->workshop->name }}</div>
                        <small class="text-muted">{{ $service->workshop->city }}</small>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Service Date</label>
                        <div>{{ $service->service_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Technician</label>
                        <div>{{ $service->technician_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Mileage</label>
                        <div>{{ $service->mileage_at_service ? number_format($service->mileage_at_service) . ' km' : '—' }}</div>
                    </div>
                    @if($service->next_service_date)
                    <div class="col-12">
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-calendar-event me-2"></i>
                            Next service due: <strong>{{ $service->next_service_date->format('d M Y') }}</strong>
                        </div>
                    </div>
                    @endif
                </div>

                <hr>

                <div class="mb-3">
                    <label class="text-muted small d-block">Issue Reported</label>
                    <div>{{ $service->issue_description }}</div>
                </div>
                <div>
                    <label class="text-muted small d-block">Repair Done</label>
                    <div>{{ $service->repair_details }}</div>
                </div>
            </div>
        </div>

        {{-- Parts Used --}}
        <div class="card mb-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-gear me-2"></i>Parts Used</h6>
            </div>
            <div class="card-body p-0">
                @if($service->serviceParts->count())
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Part Name</th><th>Category</th><th>Qty</th><th>Unit Price</th><th>Total</th><th></th></tr>
                    </thead>
                    <tbody>
                    @foreach($service->serviceParts as $sp)
                    <tr>
                        <td>{{ $sp->part->name ?? '—' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $sp->part->category ?? 'General' }}</span></td>
                        <td>{{ $sp->quantity }}</td>
                        <td>${{ number_format($sp->unit_price, 2) }}</td>
                        <td class="fw-semibold">${{ number_format($sp->total_price, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('parts.removeFromService', $sp) }}" onsubmit="return confirm('Remove this part?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center py-3 mb-0">No parts recorded for this service.</p>
                @endif
            </div>
        </div>

        {{-- Add Part Form --}}
        <div class="card">
            <div class="card-header bg-white py-3"><h6 class="mb-0">Add Part to This Service</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('parts.addToService') }}" class="row g-2">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <div class="col-md-4">
                        <select name="part_id" class="form-select form-select-sm" required>
                            <option value="">— Select Part —</option>
                            @foreach(\App\Models\Part::orderBy('name')->get() as $part)
                            <option value="{{ $part->id }}" data-price="{{ $part->unit_price }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="quantity" class="form-control form-control-sm" placeholder="Qty" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="unit_price" id="quickPrice" class="form-control form-control-sm" placeholder="Price" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Add Part</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Cost Summary --}}
    <div class="col-md-4">
        <div class="card text-dark">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-semibold text-dark">Cost Summary</h6></div>
            <div class="card-body">
                <table class="table table-borderless align-middle text-dark">
                    <tr><td>Labor Cost</td><td class="text-end fw-semibold">${{ number_format($service->labor_cost, 2) }}</td></tr>
                    <tr><td>Parts Cost</td><td class="text-end fw-semibold">${{ number_format($service->parts_cost, 2) }}</td></tr>
                    <tr class="table-success fw-bold text-dark">
                        <td>Total Cost</td>
                        <td class="text-end fs-5 text-success">${{ number_format($service->total_cost, 2) }}</td>
                    </tr>
                </table>

                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Payment Status:</span>
                    @if($service->payment && $service->payment->status === 'completed')
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                    @else
                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Unpaid</span>
                    @endif
                </div>

                @if($service->payment && $service->payment->status === 'completed')
                    <div class="bg-light-subtle border rounded p-2 small mt-2">
                        <div class="text-muted">Transaction ID:</div>
                        <strong class="font-monospace text-primary">{{ $service->payment->transaction_id }}</strong>
                        <div class="text-muted mt-1">Paid on: {{ $service->payment->paid_at->format('d M Y, h:i A') }}</div>
                    </div>
                @elseif(auth()->user()->role === 'owner')
                    <a href="{{ route('payments.checkout', $service) }}" class="btn btn-success w-100 py-2 mt-2 fw-bold">
                        <i class="bi bi-credit-card me-1"></i>Pay Bill Now
                    </a>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <a href="{{ route('vehicles.show', $service->vehicle) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-car-front me-1"></i>View Vehicle History
                </a>
                <a href="{{ route('bookings.show', $service->booking) }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-calendar-check me-1"></i>View Booking
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-fill price when part selected in quick-add
document.querySelector('[name="part_id"]')?.addEventListener('change', function() {
    const price = this.options[this.selectedIndex].dataset.price;
    document.getElementById('quickPrice').value = price || '';
});
</script>
@endpush
@endsection
