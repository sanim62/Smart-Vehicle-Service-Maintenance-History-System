{{-- ================================================================ --}}
{{-- FILE: resources/views/vehicles/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'My Vehicles')
@section('page-title', 'Vehicles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">All Vehicles</h5>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Vehicle</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Vehicle</th>
                    <th>Registration</th>
                    <th>Fuel</th>
                    <th>Mileage</th>
                    <th>Maintenance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $vehicle)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div class="fw-semibold">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                    <small class="text-muted">{{ $vehicle->year }} · {{ ucfirst($vehicle->color) }}</small>
                </td>
                <td><code>{{ $vehicle->registration_number }}</code></td>
                <td>{{ ucfirst($vehicle->fuel_type) }}</td>
                <td>{{ number_format($vehicle->mileage) }} km</td>
                <td>
                    @php $ms = $vehicle->maintenance_status; @endphp
                    <span class="fw-semibold {{ $ms === 'Normal' ? 'text-success' : ($ms === 'Due Soon' ? 'text-warning' : ($ms === 'Overdue' ? 'text-danger' : 'text-muted')) }}">
                        {{ $ms }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $vehicle->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($vehicle->status) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No vehicles found. <a href="{{ route('vehicles.create') }}">Add your first vehicle</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($vehicles->hasPages())
    <div class="card-footer">{{ $vehicles->links() }}</div>
    @endif
</div>
@endsection
