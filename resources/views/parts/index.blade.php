{{-- ================================================================ --}}
{{-- FILE: resources/views/parts/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Parts')
@section('page-title', 'Parts Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Parts Catalog</h5>
    <a href="{{ route('parts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Part</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Part Name</th>
                    <th>Part Number</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($parts as $part)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $part->name }}</td>
                <td><code>{{ $part->part_number ?? '—' }}</code></td>
                <td>{{ $part->category ?? '—' }}</td>
                <td>{{ ucfirst($part->unit) }}</td>
                <td class="fw-semibold">৳{{ number_format($part->unit_price, 2) }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('parts.edit', $part) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('parts.destroy', $part) }}" onsubmit="return confirm('Delete this part?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No parts in catalog yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($parts->hasPages())
    <div class="card-footer">{{ $parts->links() }}</div>
    @endif
</div>
@endsection
