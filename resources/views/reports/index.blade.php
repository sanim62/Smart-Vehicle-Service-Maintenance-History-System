@extends('layouts.app')
@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')

{{-- Monthly Trend --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Service Trend (Last 12 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Cost Per Vehicle --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>Cost Per Vehicle</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Vehicle</th><th>Services</th><th>Total Spent</th></tr>
                    </thead>
                    <tbody>
                    @forelse($costPerVehicle as $v)
                    <tr>
                        <td>
                            <a href="{{ route('vehicles.show', $v->id) }}">{{ $v->make }} {{ $v->model }}</a>
                            <div><small class="text-muted">{{ $v->registration_number }}</small></div>
                        </td>
                        <td>{{ $v->services_count }}</td>
                        <td class="fw-semibold text-success">৳{{ number_format($v->services_sum_total_cost ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Parts --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Most Used Parts</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Part</th><th>Qty Used</th><th>Total Value</th></tr>
                    </thead>
                    <tbody>
                    @forelse($topParts as $part)
                    <tr>
                        <td>{{ $part->name }}</td>
                        <td>{{ number_format($part->total_qty) }}</td>
                        <td class="fw-semibold">৳{{ number_format($part->total_value) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No parts data yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Workshop Performance --}}
<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-shop me-2"></i>Workshop Performance</h6>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Workshop</th><th>City</th><th>Services Completed</th><th>Total Revenue</th></tr>
            </thead>
            <tbody>
            @forelse($workshopStats as $w)
            <tr>
                <td><a href="{{ route('workshops.show', $w->id) }}">{{ $w->name }}</a></td>
                <td>{{ $w->city }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:6px">
                            @php $max = $workshopStats->max('services_count') ?: 1 @endphp
                            <div class="progress-bar" style="width:{{ ($w->services_count / $max) * 100 }}%"></div>
                        </div>
                        {{ $w->services_count }}
                    </div>
                </td>
                <td class="fw-semibold text-success">৳{{ number_format($w->services_sum_total_cost ?? 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No workshop data yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthlyTrend->reverse()->values());

const labels = monthlyData.map(d => {
    const months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${months[d.month]} ${d.year}`;
});

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Services',
                data: monthlyData.map(d => d.total),
                backgroundColor: '#3b82f620',
                borderColor: '#3b82f6',
                borderWidth: 2,
                type: 'bar',
                yAxisID: 'y',
            },
            {
                label: 'Revenue (৳)',
                data: monthlyData.map(d => d.revenue),
                borderColor: '#10b981',
                backgroundColor: '#10b98115',
                borderWidth: 2,
                type: 'line',
                fill: true,
                tension: 0.4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y:  { type: 'linear', display: true, position: 'left',  title: { display: true, text: 'Services' } },
            y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Revenue (৳)' }, grid: { drawOnChartArea: false } }
        }
    }
});
</script>
@endpush
