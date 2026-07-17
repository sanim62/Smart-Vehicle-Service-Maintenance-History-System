@extends('layouts.app')
@section('title', 'Find Nearby Workshops')
@section('page-title', 'Find Nearby Workshops & Service Hubs')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
    .workshop-card-item {
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .workshop-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Interactive Workshop Finder</h5>
        <p class="text-muted small mb-0">Locate trusted repair centers near you and book appointments directly on the map.</p>
    </div>
    <div class="d-flex gap-2">
        <button id="locate-me-btn" class="btn btn-outline-primary"><i class="bi bi-crosshair me-1"></i>Locate Me</button>
        <a href="{{ route('workshops.index') }}" class="btn btn-secondary"><i class="bi bi-list-ul me-1"></i>List View</a>
    </div>
</div>

<div class="row g-4">
    {{-- Left Column: Map --}}
    <div class="col-lg-8">
        <div class="card p-2 shadow-sm">
            <div id="map"></div>
        </div>
    </div>

    {{-- Right Column: Filter & Nearby Cards --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-funnel me-2 text-primary"></i>Filter Workshops</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">Search by Name or City</label>
                    <input type="text" id="search-filter" class="form-control" placeholder="Type city or workshop name...">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-shop me-2 text-success"></i>Nearby Centers</h6>
                <span id="workshop-count" class="badge bg-primary rounded-pill">0</span>
            </div>
            <div class="card-body p-2" style="max-height: 420px; overflow-y: auto;" id="workshop-list-container">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading workshops...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Default center (Dhaka / Central coords, falls back cleanly)
    const map = L.map('map').setView([23.8103, 90.4125], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let allWorkshops = [];
    let markersMap = {};

    // Custom Icon for Workshops
    const customIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -34]
    });

    // Fetch locations from API
    fetch("{{ route('workshops.api') }}")
        .then(response => response.json())
        .then(data => {
            allWorkshops = data;
            renderMapAndList(allWorkshops);
        })
        .catch(err => console.error("Error loading workshops:", err));

    function renderMapAndList(workshops) {
        // Clear existing markers
        Object.values(markersMap).forEach(m => map.removeLayer(m));
        markersMap = {};

        const listContainer = document.getElementById('workshop-list-container');
        document.getElementById('workshop-count').innerText = workshops.length;
        listContainer.innerHTML = '';

        if (workshops.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-geo fs-3 d-block mb-1"></i>No matching workshops found.</div>';
            return;
        }

        const bounds = L.latLngBounds();

        workshops.forEach(w => {
            const lat = w.lat || 23.8103;
            const lng = w.lng || 90.4125;
            bounds.extend([lat, lng]);

            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 class="fw-bold mb-1" style="color: #1a56db;">${w.name}</h6>
                    <div class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>${w.address}, ${w.city}</div>
                    <div class="small text-muted mb-2"><i class="bi bi-telephone me-1"></i>${w.phone}</div>
                    <div class="d-flex gap-1 mt-2">
                        <a href="${w.url}" class="btn btn-xs btn-primary text-white text-decoration-none px-2 py-1 fs-7 rounded">Details</a>
                        <a href="${w.book_url}" class="btn btn-xs btn-success text-white text-decoration-none px-2 py-1 fs-7 rounded">Book Now</a>
                    </div>
                </div>
            `;

            const marker = L.marker([lat, lng], {icon: customIcon})
                .addTo(map)
                .bindPopup(popupContent);

            markersMap[w.id] = marker;

            // Render Card in Sidebar
            const card = document.createElement('div');
            card.className = 'card mb-2 p-3 workshop-card-item border';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-bold mb-1 text-primary">${w.name}</h6>
                    <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1 text-danger"></i>${w.city}</span>
                </div>
                <div class="small text-muted mb-2">${w.address}</div>
                <div class="d-flex gap-2 align-items-center mt-2">
                    <a href="${w.url}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>View</a>
                    <a href="${w.book_url}" class="btn btn-sm btn-success flex-fill text-white"><i class="bi bi-calendar-plus me-1"></i>Book</a>
                </div>
            `;

            card.addEventListener('click', (e) => {
                if (e.target.tagName !== 'A' && e.target.tagName !== 'I') {
                    map.flyTo([lat, lng], 14, { duration: 1.2 });
                    marker.openPopup();
                }
            });

            listContainer.appendChild(card);
        });

        if (workshops.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // Search filter listener
    document.getElementById('search-filter').addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = allWorkshops.filter(w => 
            w.name.toLowerCase().includes(query) || 
            w.city.toLowerCase().includes(query) ||
            w.address.toLowerCase().includes(query)
        );
        renderMapAndList(filtered);
    });

    // Geolocation button
    document.getElementById('locate-me-btn').addEventListener('click', () => {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.flyTo([lat, lng], 14);
                L.circle([lat, lng], { radius: 500, color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.2 }).addTo(map)
                    .bindPopup("Your Current Location").openPopup();
            }, () => {
                alert("Could not retrieve your current location.");
            });
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    });
});
</script>
@endpush
