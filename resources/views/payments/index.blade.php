@extends('layouts.app')
@section('title', 'Payment History & Transactions')
@section('page-title', 'Payment History & Receipts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold"><i class="bi bi-journal-check me-2 text-success"></i>Transaction History</h5>
        <p class="text-muted small mb-0">Track all completed repairs, digital receipts, and platform commission breakdown.</p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Transaction ID</th>
                        <th>User / Payer</th>
                        <th>Workshop Center</th>
                        <th>Service Bill</th>
                        <th>2.5% Authority Fee</th>
                        <th>Workshop Payout</th>
                        <th>Status</th>
                        <th>Date Paid</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>
                            <strong class="font-monospace text-primary">{{ $p->transaction_id }}</strong>
                            <div class="small text-muted"><span class="badge bg-light text-dark border">{{ strtoupper($p->payment_method) }}</span></div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->user->name }}</div>
                            <div class="small text-muted">{{ $p->user->email }}</div>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $p->workshop->name }}</strong>
                            <div class="small text-muted">{{ $p->workshop->city }}</div>
                        </td>
                        <td class="fw-bold text-dark">${{ number_format($p->total_amount, 2) }}</td>
                        <td><span class="badge bg-info-subtle text-info fw-semibold">${{ number_format($p->commission_amount, 2) }}</span></td>
                        <td><span class="badge bg-success-subtle text-success fw-semibold">${{ number_format($p->workshop_amount, 2) }}</span></td>
                        <td>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $p->paid_at ? $p->paid_at->format('d M Y, h:i A') : $p->created_at->format('d M Y') }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-receipt fs-1 text-secondary opacity-25 d-block mb-2"></i>
                            No payment transactions recorded yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($payments->hasPages())
<div class="mt-3">{{ $payments->links() }}</div>
@endif
@endsection
