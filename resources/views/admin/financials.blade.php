@extends('layouts.app')
@section('title', 'Authority Financials & Revenue')
@section('page-title', 'Authority Financials & Commission Revenue')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Total Platform GMV</div>
                    <h3 class="fw-bold mb-0 text-primary">${{ number_format($stats['totalProcessed'], 2) }}</h3>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Authority 2.5% Revenue</div>
                    <h3 class="fw-bold mb-0 text-success">${{ number_format($stats['totalCommission'], 2) }}</h3>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-piggy-bank-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-info border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Workshop Disbursals (97.5%)</div>
                    <h3 class="fw-bold mb-0 text-info">${{ number_format($stats['workshopPayouts'], 2) }}</h3>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-building-down"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Processed Transactions</div>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($stats['totalPayments']) }}</h3>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2 text-primary"></i>Official Revenue & Commission Ledger</h6>
        <span class="badge bg-success-subtle text-success border border-success px-3 py-2"><i class="bi bi-shield-check me-1"></i>2.5% Rate Active</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Txn Reference</th>
                        <th>User / Customer</th>
                        <th>Target Workshop</th>
                        <th>Gross Bill</th>
                        <th>Authority Fee (2.5%)</th>
                        <th>Workshop Payout (97.5%)</th>
                        <th>Method</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td class="font-monospace fw-bold text-primary">{{ $p->transaction_id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->user->name }}</div>
                            <div class="small text-muted">{{ $p->user->email }}</div>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $p->workshop->name }}</strong>
                            <div class="small text-muted">{{ $p->workshop->city }}</div>
                        </td>
                        <td class="fw-bold">${{ number_format($p->total_amount, 2) }}</td>
                        <td><strong class="text-success">${{ number_format($p->commission_amount, 2) }}</strong></td>
                        <td><span class="text-secondary">${{ number_format($p->workshop_amount, 2) }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper($p->payment_method) }}</span></td>
                        <td><small class="text-muted">{{ $p->paid_at ? $p->paid_at->format('d M Y') : $p->created_at->format('d M Y') }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No financial ledger entries yet.</td>
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
