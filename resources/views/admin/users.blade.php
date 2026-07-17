@extends('layouts.admin')
@section('title', 'Manage Users')
@section('page-title', 'User Management')

@section('content')

<div class="card">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-people-fill me-2"></i>All Registered Users ({{ $users->total() }})</h6>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name or email…" style="width:220px">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Vehicles</th>
                    <th>Bookings</th>
                    <th>Joined</th>
                    <th>Change Role</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-muted small">{{ $user->id }}</td>
                <td>
                    <div class="fw-semibold">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                </td>
                <td>{{ $user->phone ?? '—' }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'text-bg-danger' : ($user->role === 'workshop' ? 'text-bg-info' : 'text-bg-success') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->vehicles_count }}</td>
                <td>{{ $user->bookings_count }}</td>
                <td><small>{{ $user->created_at->format('d M Y') }}</small></td>
                <td>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-1">
                        @csrf @method('PATCH')
                        <select name="role" class="form-select form-select-sm" style="width:110px">
                            @foreach(['owner','workshop','admin'] as $role)
                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-primary">Set</button>
                    </form>
                    @else
                    <span class="text-muted small">You</span>
                    @endif
                </td>
                <td>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                          onsubmit="return confirm('DELETE user {{ addslashes($user->name) }}?\n\nThis will permanently remove their account, vehicles, and all associated data. This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" title="Delete User">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </form>
                    @else
                    <span class="text-muted small">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">
                <i class="bi bi-people display-6 d-block mb-2"></i>No users found.
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

@endsection
