@extends('layouts.app')
@section('title', 'Configure Working Hours')
@section('page-title', 'Configure Working Hours')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark fw-bold"><i class="bi bi-clock me-2 text-primary"></i>Weekly Operating Hours for "{{ $workshop->name }}"</h6>
                <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back Profile</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('workshops.hours.save', $workshop) }}">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table align-middle text-dark">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Status</th>
                                    <th>Open Time</th>
                                    <th>Close Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hours as $h)
                                <tr>
                                    <td>
                                        <strong>{{ $h->day_name }}</strong>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="hours[{{ $h->day_of_week }}][is_closed]" value="1">
                                            <input class="form-check-input closed-toggle" type="checkbox" 
                                                   name="hours[{{ $h->day_of_week }}][is_closed]" 
                                                   value="0" 
                                                   id="closed-{{ $h->day_of_week }}" 
                                                   {{ !$h->is_closed ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="closed-{{ $h->day_of_week }}">
                                                {{ !$h->is_closed ? 'Open' : 'Closed' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" name="hours[{{ $h->day_of_week }}][open_time]" 
                                               class="form-control form-control-sm time-input" 
                                               value="{{ $h->open_time ? date('H:i', strtotime($h->open_time)) : '' }}"
                                               {{ $h->is_closed ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" name="hours[{{ $h->day_of_week }}][close_time]" 
                                               class="form-control form-control-sm time-input" 
                                               value="{{ $h->close_time ? date('H:i', strtotime($h->close_time)) : '' }}"
                                               {{ $h->is_closed ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save Working Hours</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.closed-toggle').forEach(el => {
        el.addEventListener('change', (e) => {
            const row = e.target.closest('tr');
            const timeInputs = row.querySelectorAll('.time-input');
            const label = row.querySelector('.form-check-label');
            
            if (e.target.checked) {
                timeInputs.forEach(input => input.disabled = false);
                label.innerText = 'Open';
            } else {
                timeInputs.forEach(input => {
                    input.disabled = true;
                    input.value = '';
                });
                label.innerText = 'Closed';
            }
        });
    });
});
</script>
@endpush
