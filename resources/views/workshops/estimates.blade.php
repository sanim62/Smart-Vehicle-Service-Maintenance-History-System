@extends('layouts.app')
@section('title', 'Configure Service Estimates & Pricing')
@section('page-title', 'Configure Service Estimates & Pricing')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card text-dark">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Service Menu Pricing Estimates for "{{ $workshop->name }}"</h6>
                <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back Profile</a>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">Set upfront price ranges and estimated completion durations for the service types you offer. Leave prices empty if you do not offer a specific service category.</p>
                
                <form method="POST" action="{{ route('workshops.estimates.save', $workshop) }}">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table align-middle text-dark">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Service Type</th>
                                    <th>Min Price (৳)</th>
                                    <th>Max Price (৳)</th>
                                    <th>Est. Duration (Hours)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $key => $name)
                                @php $est = $estimates->get($key); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $name }}</strong>
                                        <div class="small text-muted">Category code: <code>{{ $key }}</code></div>
                                    </td>
                                    <td>
                                        <input type="number" name="estimates[{{ $key }}][min_price]" 
                                               class="form-control form-control-sm" 
                                               placeholder="Min ৳"
                                               value="{{ $est ? $est->min_price : '' }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="estimates[{{ $key }}][max_price]" 
                                               class="form-control form-control-sm" 
                                               placeholder="Max ৳"
                                               value="{{ $est ? $est->max_price : '' }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.5" name="estimates[{{ $key }}][duration_hours]" 
                                               class="form-control form-control-sm" 
                                               placeholder="e.g. 2.5"
                                               value="{{ $est ? $est->duration_hours : '1.0' }}" min="0.5" max="72">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save Service Pricing Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
