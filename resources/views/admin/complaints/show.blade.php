@extends('layouts.admin')
@section('title', 'Ticket #' . $complaint->id)
@section('page-title', 'Ticket #' . $complaint->id . ' — ' . Str::limit($complaint->subject, 40))

@section('content')

<div class="row g-3">
    {{-- Left: ticket content & Actions --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark">{{ $complaint->subject }}</h6>
                <span class="badge {{ $complaint->typeBadgeClass() }}">{{ ucfirst($complaint->type) }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-person-circle fs-5 text-muted"></i>
                    <div>
                        <span class="fw-semibold text-dark">{{ $complaint->user->name ?? 'Deleted User' }}</span>
                        <span class="text-muted ms-2 small">{{ $complaint->user->email ?? '' }}</span>
                    </div>
                    <span class="ms-auto text-muted small">{{ $complaint->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="bg-light rounded p-3 text-dark border" style="white-space: pre-wrap; line-height: 1.7; font-size: 1rem;">{{ $complaint->message }}</div>
            </div>
        </div>

        {{-- Admin reply section --}}
        @if($complaint->admin_reply)
        <div class="card mb-3 border-success">
            <div class="card-header py-2 bg-success-subtle border-success">
                <span class="fw-semibold text-success"><i class="bi bi-shield-check me-1"></i>Official Authority Reply</span>
                <small class="text-muted ms-2">{{ $complaint->replied_at?->format('d M Y H:i') }}</small>
            </div>
            <div class="card-body">
                <div class="text-dark" style="white-space: pre-wrap; line-height: 1.7;">{{ $complaint->admin_reply }}</div>
            </div>
        </div>
        @endif

        {{-- Reply form --}}
        <div class="card mb-3">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-reply-fill me-2 text-primary"></i>Response Panel</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.complaints.reply', $complaint) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Authority Response</label>
                        <textarea name="admin_reply" class="form-control" rows="4"
                                  placeholder="Type your reply to the user…" required>{{ old('admin_reply', $complaint->admin_reply) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="status" value="in_review" class="btn btn-warning">
                            <i class="bi bi-hourglass-split me-1"></i>Reply & Mark In Review
                        </button>
                        <button type="submit" name="status" value="resolved" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Reply & Resolve Issue
                        </button>
                        <button type="submit" name="status" value="closed" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reply & Close
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Warning Dispatch Panel --}}
        @if($complaint->workshop)
        <div class="card border-danger mb-3">
            <div class="card-header bg-danger-subtle border-danger py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Issue Administrative Warning to Workshop Owner</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.complaints.warning', $complaint) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Warning Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Violation of service rules..." required value="Official Warning: Complaint ID #{{ $complaint->id }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Severity Level</label>
                            <select name="severity" class="form-select" required>
                                <option value="low">Low (First notice)</option>
                                <option value="medium" selected>Medium (Action required)</option>
                                <option value="high">High (Final warning)</option>
                                <option value="critical">Critical (Suspension threat)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warning message content details</label>
                        <textarea name="warning_message" class="form-control" rows="4" placeholder="Detail the violation, necessary actions to resolve the user's issue, and consequences of failure..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-send me-1"></i>Dispatch Official Warning
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: metadata, target workshop, and warning logs --}}
    <div class="col-lg-4">
        {{-- Target Workshop --}}
        @if($complaint->workshop)
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning-subtle border-warning py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-shop me-2 text-warning"></i>Target Workshop</h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1 text-dark">{{ $complaint->workshop->name }}</h6>
                <div class="small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $complaint->workshop->address }}, {{ $complaint->workshop->city }}</div>
                
                <table class="table table-sm table-borderless small mb-3">
                    <tr><td class="fw-semibold text-muted">Owner Name</td><td class="text-dark">{{ $complaint->workshop->owner_name }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Phone</td><td class="text-dark">{{ $complaint->workshop->phone }}</td></tr>
                    <tr><td class="fw-semibold text-muted">License</td><td class="text-dark">{{ $complaint->workshop->license_number ?? '—' }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Status</td>
                        <td>
                            @if($complaint->workshop->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($complaint->workshop->status === 'suspended')
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($complaint->workshop->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- Status modifier --}}
                <div class="pt-3 border-top">
                    <form method="POST" action="{{ route('admin.workshops.status', $complaint->workshop) }}">
                        @csrf @method('PATCH')
                        <label class="form-label small text-muted">Administrative Action on Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select form-select-sm">
                                <option value="active" {{ $complaint->workshop->status === 'active' ? 'selected' : '' }}>Activate</option>
                                <option value="suspended" {{ $complaint->workshop->status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                <option value="inactive" {{ $complaint->workshop->status === 'inactive' ? 'selected' : '' }}>Deactivate</option>
                            </select>
                            <button class="btn btn-sm btn-outline-danger" type="submit">Apply</button>
                        </div>
                    </form>
                </div>

                {{-- Verification modifier --}}
                <div class="pt-3 mt-2 border-top">
                    <form method="POST" action="{{ route('admin.workshops.verify', $complaint->workshop) }}">
                        @csrf
                        <input type="hidden" name="is_verified" value="{{ $complaint->workshop->is_verified ? 0 : 1 }}">
                        <label class="form-label small text-muted">Platform Badge Verification</label>
                        <button class="btn btn-sm w-100 {{ $complaint->workshop->is_verified ? 'btn-outline-warning' : 'btn-success text-white' }}" type="submit">
                            @if($complaint->workshop->is_verified)
                                <i class="bi bi-patch-minus me-1"></i> Revoke Verification
                            @else
                                <i class="bi bi-patch-check-fill me-1"></i> Verify Workshop Account
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Target Workshop Warning history count --}}
        <div class="card mb-3">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-clock-history me-2 text-info"></i>Workshop Warning Logs</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    @forelse($complaint->workshop->warnings as $w)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $w->severityBadgeClass() }}">{{ ucfirst($w->severity) }}</span>
                                <small class="text-muted">{{ $w->created_at->format('d M Y') }}</small>
                            </div>
                            <strong class="text-dark d-block">{{ $w->subject }}</strong>
                            <span class="text-muted">{{ Str::limit($w->warning_message, 80) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted">No warnings issued to this workshop yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        @endif

        {{-- Ticket details --}}
        <div class="card mb-3">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="mb-0 fw-semibold text-dark">Ticket Metadata</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted fw-semibold">ID</td><td class="text-dark">#{{ $complaint->id }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Status</td>
                        <td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ ucwords(str_replace('_',' ',$complaint->status)) }}</span></td>
                    </tr>
                    <tr><td class="text-muted fw-semibold">Type</td>
                        <td><span class="badge {{ $complaint->typeBadgeClass() }}">{{ ucfirst($complaint->type) }}</span></td>
                    </tr>
                    <tr><td class="text-muted fw-semibold">Submitted</td><td class="text-dark">{{ $complaint->created_at->format('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.complaints') }}" class="btn btn-outline-secondary w-100 mb-3">
            <i class="bi bi-arrow-left me-1"></i>Back to Inbox
        </a>
    </div>
</div>

@endsection
