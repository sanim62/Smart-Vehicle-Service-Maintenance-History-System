@extends('layouts.app')
@section('title', 'My Complaints & Dispute Cases')
@section('page-title', 'My Complaints & Dispute Cases')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold"><i class="bi bi-headset me-2 text-danger"></i>Support Ticket Center</h5>
        <p class="text-muted small mb-0">Track responses to your complaints and raised dispute issues regarding workshop services.</p>
    </div>
    <a href="{{ route('complaints.create') }}" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i>File New Complaint</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-ticket-perforated me-2 text-primary"></i>My Support Tickets</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date Filed</th>
                        <th>Subject</th>
                        <th>Associated Workshop</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Replies</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr class="text-dark">
                        <td>{{ $c->created_at->format('d M Y') }}</td>
                        <td>
                            <strong class="text-primary">{{ $c->subject }}</strong>
                            <p class="text-muted small mb-0 text-truncate" style="max-width: 300px;">{{ $c->message }}</p>
                        </td>
                        <td>{{ $c->workshop->name ?? 'Platform Support' }}</td>
                        <td>
                            <span class="badge {{ $c->typeBadgeClass() }}">{{ ucfirst($c->type) }}</span>
                        </td>
                        <td>
                            @if($c->status === 'open')
                                <span class="badge bg-danger">Open</span>
                            @elseif($c->status === 'in_review')
                                <span class="badge bg-warning text-dark">In Review</span>
                            @elseif($c->status === 'resolved')
                                <span class="badge bg-success">Resolved</span>
                            @else
                                <span class="badge bg-secondary">Closed</span>
                            @endif
                        </td>
                        <td>
                            @if($c->admin_reply)
                                <button class="btn btn-xs btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#reply-{{ $c->id }}">
                                    <i class="bi bi-chat-text-fill me-1"></i>View Reply
                                </button>
                            @else
                                <span class="text-muted small">No reply yet</span>
                            @endif
                        </td>
                    </tr>
                    @if($c->admin_reply)
                    <tr class="collapse bg-light" id="reply-{{ $c->id }}">
                        <td colspan="6" class="p-3">
                            <div class="card p-3 border-info shadow-none bg-white">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="fw-bold text-info mb-0"><i class="bi bi-shield-check me-1"></i>Official Admin Reply</h6>
                                    <small class="text-muted">{{ $c->replied_at ? $c->replied_at->format('d M Y H:i') : '' }}</small>
                                </div>
                                <p class="mb-0 text-dark small" style="white-space: pre-wrap;">{{ $c->admin_reply }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-chat-left-dots text-muted opacity-25" style="font-size: 3rem;"></i>
                            <p class="mt-2 mb-0">You haven't filed any complaints yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($complaints->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $complaints->links() }}
    </div>
    @endif
</div>
@endsection
