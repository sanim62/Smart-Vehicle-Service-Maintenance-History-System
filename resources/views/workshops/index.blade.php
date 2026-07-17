@extends('layouts.app')
@section('title', 'Workshops')
@section('page-title', 'Workshops')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="mb-1 fw-bold text-dark">All Service Workshops</h5>
        <p class="text-muted small mb-0">Browse workshops, search by city or category, and book diagnostic servicing.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('workshops.map') }}" class="btn btn-outline-danger"><i class="bi bi-map me-1"></i>Interactive Map</a>
        <a href="{{ route('workshops.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Register Workshop</a>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('workshops.index') }}" class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control text-dark" placeholder="Search by workshop name, city or location address...">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
@forelse($workshops as $workshop)
<div class="col-md-4">
    <div class="card h-100 text-dark">
        @if($workshop->photos && count($workshop->photos) > 0)
            <img src="{{ $workshop->photos[0] }}" class="card-img-top" alt="{{ $workshop->name }}" style="height: 160px; object-fit: cover;">
        @endif
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">
                        {{ $workshop->name }}
                        @if($workshop->is_verified)
                            <i class="bi bi-patch-check-fill text-primary" title="Verified Workshop" style="font-size: 0.95rem;"></i>
                        @endif
                    </h6>
                    <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $workshop->city }}</div>
                </div>
                <div class="d-flex flex-column align-items-end gap-1">
                    <span class="badge {{ $workshop->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($workshop->status) }}
                    </span>
                    @if($workshop->isOpenNow())
                        <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.75rem;">Open Now</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger" style="font-size: 0.75rem;">Closed</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-1 mt-2">
                <span class="small">{!! $workshop->starsHtml() !!}</span>
                <span class="text-muted small">({{ $workshop->total_reviews }})</span>
            </div>

            <hr class="my-2">

            <div class="small text-muted mb-1"><i class="bi bi-person me-1"></i>{{ $workshop->owner_name }}</div>
            <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $workshop->phone }}</div>
            @if($workshop->email)
            <div class="small text-muted mb-2"><i class="bi bi-envelope me-1"></i>{{ $workshop->email }}</div>
            @endif

            <div class="mb-3">
                @foreach(json_decode($workshop->service_categories, true) ?? [] as $cat)
                <span class="badge bg-light text-dark border me-1 mb-1">{{ ucfirst(str_replace('_',' ',$cat)) }}</span>
                @endforeach
            </div>

            <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-eye me-1"></i>View Details
            </a>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="text-center text-muted py-5">
        <i class="bi bi-shop" style="font-size:3rem;opacity:.2"></i>
        <p class="mt-2">No workshops registered yet.</p>
        <a href="{{ route('workshops.create') }}" class="btn btn-primary">Register a Workshop</a>
    </div>
</div>
@endforelse
</div>

@if($workshops->hasPages())
<div class="mt-3">{{ $workshops->links() }}</div>
@endif
@endsection
