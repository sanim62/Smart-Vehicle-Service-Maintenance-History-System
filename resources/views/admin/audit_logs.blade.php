@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'System Audit Logs')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-2"></i>System Audit Logs</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Model</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td><small>{{ $log->created_at->format('d M Y H:i') }}</small></td>
                <td>{{ $log->user->name ?? '<em>System</em>' }}</td>
                <td>
                    <span class="badge {{ $log->action === 'created' ? 'bg-success' : ($log->action === 'deleted' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ ucfirst($log->action) }}
                    </span>
                </td>
                <td><code>{{ $log->model_type }}</code></td>
                <td>{{ $log->model_id ?? '—' }}</td>
                <td><small class="text-muted">{{ $log->ip_address ?? '—' }}</small></td>
                <td>
                    @if($log->new_values || $log->old_values)
                    <button class="btn btn-xs btn-sm btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                        View
                    </button>

                    {{-- Modal --}}
                    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Audit Log #{{ $log->id }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        @if($log->old_values)
                                        <div class="col-md-6">
                                            <h6 class="text-danger">Before</h6>
                                            <pre class="bg-light p-2 rounded small">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                        @endif
                                        @if($log->new_values)
                                        <div class="col-md-6">
                                            <h6 class="text-success">After</h6>
                                            <pre class="bg-light p-2 rounded small">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No audit logs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
