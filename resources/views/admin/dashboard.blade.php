@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['totalUsers'] }}</div>
                    <div class="text-muted small">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff1f2;color:#ef4444;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['openComplaints'] }}</div>
                    <div class="text-muted small">Open Complaints</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-piggy-bank-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">${{ number_format($stats['totalCommission'], 2) }}</div>
                    <div class="text-muted small">Commission (2.5%)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['totalWarnings'] }}</div>
                    <div class="text-muted small">Warnings Issued</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main section --}}
<div class="row g-3 mb-4">
    {{-- Recent Payments --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-bank2 me-2 text-success"></i>Recent Payments & Commission Split (2.5%)</h6>
                <a href="{{ route('admin.financials') }}" class="btn btn-sm btn-outline-success">Financial Ledger</a>
            </div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0 text-dark">
                    <thead class="table-light">
                        <tr>
                            <th>Txn Reference</th>
                            <th>Customer</th>
                            <th>Workshop</th>
                            <th>Gross Total</th>
                            <th>Authority Fee (2.5%)</th>
                            <th>Workshop Payout</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recentPayments as $p)
                    <tr>
                        <td class="font-monospace fw-bold text-primary">{{ $p->transaction_id }}</td>
                        <td>{{ $p->user->name }}</td>
                        <td>{{ $p->workshop->name }}</td>
                        <td class="fw-bold">${{ number_format($p->total_amount, 2) }}</td>
                        <td class="text-success fw-bold">${{ number_format($p->commission_amount, 2) }}</td>
                        <td class="text-muted">${{ number_format($p->workshop_amount, 2) }}</td>
                        <td><small class="text-muted">{{ $p->created_at->format('d M Y') }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No payments processed yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent Complaints --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-inbox-fill me-2 text-danger"></i>Recent Complaints & Requests</h6>
                <a href="{{ route('admin.complaints') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 text-dark align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>From</th>
                            <th>Target Center</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recentComplaints as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->user->name ?? '—' }}</td>
                        <td>
                            @if($c->workshop)
                                <span class="badge bg-secondary-subtle text-secondary small"><i class="bi bi-shop me-1"></i>{{ $c->workshop->name }}</span>
                            @else
                                <span class="text-muted small">Platform</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.complaints.show', $c) }}" class="text-decoration-none fw-semibold text-primary">
                                {{ Str::limit($c->subject, 25) }}
                            </a>
                        </td>
                        <td><span class="badge {{ $c->typeBadgeClass() }}">{{ ucfirst($c->type) }}</span></td>
                        <td><span class="badge {{ $c->statusBadgeClass() }}">{{ ucwords(str_replace('_',' ',$c->status)) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No complaints yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Registrations --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-person-plus-fill me-2 text-primary"></i>New Registrations</h6>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">All Users</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 text-dark align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recentUsers as $u)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $u->name }}</div>
                            <div class="text-muted small">{{ $u->email }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'text-bg-danger' : ($u->role === 'workshop' ? 'text-bg-info' : 'text-bg-success') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $u->created_at->format('d M Y') }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">No users yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
