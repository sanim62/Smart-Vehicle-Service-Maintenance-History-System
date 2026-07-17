@extends('layouts.app')
@section('title', 'Submit a Request / Complaint')
@section('page-title', 'Submit a Request / Complaint')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        {{-- Prior submissions --}}
        @if($complaints->count())
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Your Previous Submissions</h6>
            </div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Target / Workshop</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($complaints as $c)
                    <tr>
                        <td>
                            <strong class="text-dark">{{ Str::limit($c->subject, 35) }}</strong>
                        </td>
                        <td>
                            @if($c->workshop)
                                <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-shop me-1"></i>{{ $c->workshop->name }}</span>
                            @else
                                <span class="text-muted small">General Platform</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $c->typeBadgeClass() }}">{{ ucfirst($c->type) }}</span></td>
                        <td><span class="badge {{ $c->statusBadgeClass() }}">{{ ucwords(str_replace('_',' ',$c->status)) }}</span></td>
                        <td><small class="text-muted">{{ $c->created_at->format('d M Y') }}</small></td>
                    </tr>
                    @if($c->admin_reply)
                    <tr class="table-success">
                        <td colspan="5" class="ps-4">
                            <i class="bi bi-shield-check me-1 text-success"></i>
                            <strong>Admin replied:</strong> {{ $c->admin_reply }}
                            @if($c->replied_at)
                                <span class="text-muted small ms-2">({{ $c->replied_at->format('d M Y') }})</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- New submission form --}}
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-send-fill me-2 text-primary"></i>New Submission</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('complaints.store') }}">
                    @csrf
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type of Submission</label>
                            <select name="type" class="form-select" required>
                                <option value="">— Select type —</option>
                                <option value="complaint" {{ old('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                                <option value="demand"    {{ old('type') === 'demand'    ? 'selected' : '' }}>Demand</option>
                                <option value="request"   {{ old('type') === 'request'   ? 'selected' : '' }}>Request</option>
                                <option value="feedback"  {{ old('type') === 'feedback'  ? 'selected' : '' }}>Feedback</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target Workshop <span class="text-muted small">(Optional)</span></label>
                            <select name="workshop_id" class="form-select">
                                <option value="">— General Platform / No Workshop —</option>
                                @foreach($workshops as $w)
                                    <option value="{{ $w->id }}" {{ old('workshop_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }} ({{ $w->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                               class="form-control" placeholder="Brief description of your issue…" required maxlength="255">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Full Message</label>
                        <textarea name="message" class="form-control" rows="6"
                                  placeholder="Describe your complaint, demand, or request in detail. Mention any specific bookings, issues, or details." required>{{ old('message') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-2"></i>Submit Request
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
