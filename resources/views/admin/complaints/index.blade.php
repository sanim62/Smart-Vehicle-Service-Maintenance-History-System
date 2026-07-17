@extends('layouts.admin')
@section('title', 'Complaints & Requests')
@section('page-title', 'Complaints & Requests Inbox')

@section('content')

{{-- Filter tabs --}}
<div class="d-flex gap-2 flex-wrap mb-3">
    @foreach(['all' => 'All', 'open' => 'Open', 'in_review' => 'In Review', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $val => $label)
    <a href="{{ route('admin.complaints', ['status' => $val]) }}"
       class="btn btn-sm {{ request('status', 'all') === $val ? 'btn-danger' : 'btn-outline-secondary' }}">
        {{ $label }}
        @if($val === 'open')<span class="badge text-bg-light ms-1">{{ \App\Models\Complaint::where('status','open')->count() }}</span>@endif
    </a>
    @endforeach
    <div class="ms-auto">
        <form method="GET" action="{{ route('admin.complaints') }}" class="d-flex gap-2">
            @if(request('status') && request('status') !== 'all')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search subject…" style="width:200px">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-inbox-fill me-2 text-danger"></i>{{ $complaints->total() }} Ticket(s)</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($complaints as $c)
            <tr class="{{ $c->status === 'open' ? 'table-warning' : '' }}">
                <td class="text-muted small">{{ $c->id }}</td>
                <td>
                    <div class="fw-semibold">{{ $c->user->name ?? '—' }}</div>
                    <div class="text-muted small">{{ $c->user->email ?? '' }}</div>
                </td>
                <td>{{ Str::limit($c->subject, 45) }}</td>
                <td><span class="badge {{ $c->typeBadgeClass() }}">{{ ucfirst($c->type) }}</span></td>
                <td><span class="badge {{ $c->statusBadgeClass() }}">{{ ucwords(str_replace('_',' ',$c->status)) }}</span></td>
                <td><small>{{ $c->created_at->format('d M Y H:i') }}</small></td>
                <td>
                    <a href="{{ route('admin.complaints.show', $c) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">
                <i class="bi bi-inbox display-6 d-block mb-2"></i>No tickets found.
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($complaints->hasPages())
    <div class="card-footer">{{ $complaints->links() }}</div>
    @endif
</div>

@endsection
