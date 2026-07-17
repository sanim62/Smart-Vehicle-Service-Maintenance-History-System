@extends('layouts.app')
@section('title', $workshop->name)
@section('page-title', 'Workshop Details')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #mini-map {
        height: 220px;
        width: 100%;
        border-radius: 8px;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="row g-3">
    {{-- Left: Details & Map --}}
    <div class="col-md-4 text-dark">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            {{ $workshop->name }}
                            @if($workshop->is_verified)
                                <i class="bi bi-patch-check-fill text-primary" title="Verified Workshop"></i>
                            @endif
                        </h5>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span>{!! $workshop->starsHtml() !!}</span>
                            <span class="text-muted small">({{ $workshop->total_reviews }})</span>
                        </div>
                        <span class="text-muted small"><i class="bi bi-geo-alt me-1 mt-2"></i>{{ $workshop->city }}</span>
                    </div>
                    @if(auth()->user()->isAdmin() || $workshop->user_id === auth()->id())
                    <a href="{{ route('workshops.edit', $workshop) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    @endif
                </div>

                <table class="table table-sm table-borderless align-middle text-dark">
                    <tr><td class="text-muted">Owner</td><td>{{ $workshop->owner_name }}</td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $workshop->phone }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $workshop->email ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Address</td><td>{{ $workshop->address }}</td></tr>
                    <tr><td class="text-muted">License</td><td>{{ $workshop->license_number ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Status</td>
                        <td>
                            @if($workshop->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($workshop->status === 'suspended')
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($workshop->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if(auth()->user()->isAdmin())
                <form method="POST" action="{{ route('admin.workshops.verify', $workshop) }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="is_verified" value="{{ $workshop->is_verified ? 0 : 1 }}">
                    <button type="submit" class="btn btn-sm {{ $workshop->is_verified ? 'btn-outline-danger' : 'btn-primary' }} w-100">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ $workshop->is_verified ? 'Remove Verification' : 'Verify Workshop Account' }}
                    </button>
                </form>
                @endif

                @if($workshop->user_id === auth()->id())
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('workshops.hours', $workshop) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-clock me-1"></i>Configure Working Hours
                    </a>
                    <a href="{{ route('workshops.estimates', $workshop) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-tags me-1"></i>Configure Pricing Menu
                    </a>
                </div>
                @endif

                @if($workshop->photos && count($workshop->photos) > 0)
                <hr>
                <div class="mb-2 fw-medium small text-muted">GALLERY</div>
                <div class="row g-1">
                    @foreach($workshop->photos as $photo)
                    <div class="col-4">
                        <img src="{{ $photo }}" class="img-fluid rounded" style="height: 60px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open(this.src)">
                    </div>
                    @endforeach
                </div>
                @endif

                <hr>
                <div class="mb-2 fw-medium small text-muted">OPERATING HOURS</div>
                <table class="table table-sm table-borderless small text-dark mb-0">
                    @forelse($workshop->hours as $h)
                    <tr>
                        <td>{{ $h->day_name }}</td>
                        <td class="text-end {{ $h->is_closed ? 'text-danger' : '' }}">{{ $h->display_hours }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-muted small">Hours not configured yet.</td></tr>
                    @endforelse
                </table>

                <hr>
                <div class="mb-2 fw-medium small text-muted">SERVICE CATEGORIES</div>
                @foreach(json_decode($workshop->service_categories, true) ?? [] as $cat)
                <span class="badge bg-primary-subtle text-primary me-1 mb-1">{{ ucfirst(str_replace('_', ' ', $cat)) }}</span>
                @endforeach

                @if($workshop->description)
                <hr>
                <p class="text-slate-500 small mb-0">{{ $workshop->description }}</p>
                @endif
            </div>
            <div class="card-footer bg-white">
                <div class="row text-center text-dark">
                    <div class="col border-end">
                        <div class="fw-bold text-primary">{{ $services->count() }}</div>
                        <small class="text-muted">Services</small>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-success">৳{{ number_format($totalEarned) }}</div>
                        <small class="text-muted">Revenue</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mini Map View --}}
        <div class="card mb-3 text-dark">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-map me-2 text-danger"></i>Location Map</h6>
            </div>
            <div class="card-body p-2">
                <div id="mini-map"></div>
            </div>
        </div>
    </div>

    {{-- Right: Warnings and Services --}}
    <div class="col-md-8 text-dark">
        {{-- Show Compliance warnings banner to owner or admin --}}
        @if(auth()->user()->isAdmin() || $workshop->user_id === auth()->id())
            @if($workshop->warnings->count() > 0)
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger-subtle border-danger py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-shield-exclamation me-2"></i>Platform Compliance Warning Alerts ({{ $workshop->warnings->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($workshop->warnings as $w)
                        <div class="list-group-item bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $w->severityBadgeClass() }}">{{ ucfirst($w->severity) }} Severity</span>
                                <small class="text-muted">Issued: {{ $w->created_at->format('d M Y') }}</small>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $w->subject }}</h6>
                            <p class="mb-0 text-muted small">{{ $w->warning_message }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        @endif

        {{-- Service Menu & Price Estimates --}}
        <div class="card mb-3 text-dark shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-tags-fill me-2 text-success"></i>Service Menu & Pricing Estimates</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Service Category</th>
                            <th>Upfront Cost Range</th>
                            <th>Est. Duration</th>
                            @if(auth()->user()->isOwner())
                                <th class="text-end px-3">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workshop->estimates as $est)
                        <tr>
                            <td>
                                <strong>{{ $categories[$est->service_type] ?? ucfirst($est->service_type) }}</strong>
                            </td>
                            <td class="fw-bold text-success">{{ $est->priceRange() }}</td>
                            <td>{{ $est->duration_hours }} hour(s)</td>
                            @if(auth()->user()->isOwner())
                                <td class="text-end px-3">
                                    <a href="{{ route('bookings.create', ['workshop_id' => $workshop->id, 'service_type' => $est->service_type]) }}" class="btn btn-xs btn-sm btn-success text-white">
                                        Book Service
                                    </a>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No price estimates published by this workshop yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Leave a Review (if completed booking exists) --}}
        @if(auth()->user()->isOwner())
            @php
                $unreviewedBooking = auth()->user()->bookings()
                    ->where('workshop_id', $workshop->id)
                    ->where('status', 'completed')
                    ->whereDoesntHave('review')
                    ->first();
            @endphp
            @if($unreviewedBooking)
            <div class="card mb-3 text-dark shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-star me-2 text-warning"></i>Leave a Review for Your Recent Service</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reviews.store', $unreviewedBooking) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Star Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                                <option value="3">⭐⭐⭐ (3 - Good)</option>
                                <option value="2">⭐⭐ (2 - Average)</option>
                                <option value="1">⭐ (1 - Poor)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Review Comment</label>
                            <textarea name="comment" class="form-control" rows="2" placeholder="Tell us about the quality of the service, staff behavior, etc..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
            @endif
        @endif

        {{-- Customer Reviews --}}
        <div class="card mb-3 text-dark shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-chat-left-text me-2 text-warning"></i>Customer Reviews & Ratings</h6>
            </div>
            <div class="card-body">
                @forelse($workshop->reviews as $rev)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <strong class="text-dark">{{ $rev->user->name }}</strong>
                            <span class="ms-2">{!! $rev->starHtml() !!}</span>
                        </div>
                        <small class="text-muted">{{ $rev->created_at->format('d M Y') }}</small>
                    </div>
                    <p class="mb-0 text-slate-600 small">{{ $rev->comment ?? 'No comment provided.' }}</p>
                </div>
                @empty
                <div class="text-center text-muted py-4">No reviews yet for this workshop.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Services --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Service Billings & Repairments</h6>
            </div>
            <div class="card-body p-0">
                @forelse($services as $service)
                <div class="border-bottom p-3 d-flex justify-content-between align-items-start text-dark">
                    <div>
                        <div class="fw-semibold">
                            {{ $service->vehicle->make ?? '—' }} {{ $service->vehicle->model ?? '' }}
                            <span class="text-muted fw-normal small ms-1">({{ $service->vehicle->registration_number ?? '' }})</span>
                        </div>
                        <div class="text-muted small mt-1">Date: {{ $service->service_date->format('d M Y') }}</div>
                        <div class="small mt-1 text-secondary">{{ Str::limit($service->issue_description, 100) }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">৳{{ number_format($service->total_cost) }}</div>
                        <div class="mt-1">
                            @if($service->payment && $service->payment->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning"><i class="bi bi-hourglass-split me-1"></i>Unpaid</span>
                                @if(auth()->user()->role === 'owner')
                                    <a href="{{ route('payments.checkout', $service) }}" class="btn btn-xs btn-sm btn-success text-white d-block mt-2">Pay Now</a>
                                @endif
                            @endif
                        </div>
                        <a href="{{ route('services.show', $service) }}" class="btn btn-xs btn-sm btn-outline-primary mt-2">Details</a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-tools" style="font-size:3rem;opacity:.2"></i>
                    <p class="mt-2">No services recorded for this workshop yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = {{ (float) ($workshop->latitude ?? 23.8103) }};
    const lng = {{ (float) ($workshop->longitude ?? 90.4125) }};
    
    const miniMap = L.map('mini-map').setView([lat, lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(miniMap);
    
    const icon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });
    
    L.marker([lat, lng], {icon: icon}).addTo(miniMap)
        .bindPopup("<strong>{{ $workshop->name }}</strong><br>{{ $workshop->city }}")
        .openPopup();
});
</script>
@endpush
