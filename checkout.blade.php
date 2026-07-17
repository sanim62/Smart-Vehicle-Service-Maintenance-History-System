@extends('layouts.app')
@section('title', 'Service Checkout & Payment')
@section('page-title', 'Service Checkout & Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i>Online Payment Checkout</h6>
                <span class="badge bg-white text-primary fw-semibold">Bill #SRV-{{ $service->id }}</span>
            </div>
            <div class="card-body p-4">
                
                {{-- Service & Vehicle Summary --}}
                <div class="row mb-4 bg-light p-3 rounded border">
                    <div class="col-sm-6 mb-2 mb-sm-0">
                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Workshop / Service Center</small>
                        <strong class="fs-6 text-primary"><i class="bi bi-shop me-1"></i>{{ $service->workshop->name }}</strong>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $service->workshop->city }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Vehicle Info</small>
                        <strong class="fs-6 text-dark"><i class="bi bi-car-front me-1"></i>{{ $service->vehicle->make }} {{ $service->vehicle->model }} ({{ $service->vehicle->year }})</strong>
                        <div class="small text-muted"><i class="bi bi-card-heading me-1"></i>{{ $service->vehicle->registration_number }}</div>
                    </div>
                </div>

                {{-- Repair & Cost Breakdown Table --}}
                <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-secondary"></i>Bill Summary</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item / Description</th>
                                <th class="text-end" style="width: 140px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Labor & Technician Charges</strong>
                                    <div class="small text-muted">{{ $service->repair_details }}</div>
                                </td>
                                <td class="text-end fw-semibold">${{ number_format($service->labor_cost, 2) }}</td>
                            </tr>
                            @if($service->serviceParts->count() > 0)
                            <tr>
                                <td>
                                    <strong>Replacement Parts & Components</strong>
                                    <ul class="mb-0 ps-3 small text-muted">
                                        @foreach($service->serviceParts as $sp)
                                            <li>{{ $sp->part->name }} (x{{ $sp->quantity }}) — ${{ number_format($sp->total_price, 2) }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-end fw-semibold">${{ number_format($service->parts_cost, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr class="table-active">
                                <td class="fw-bold fs-5 text-dark">Total Amount Due</td>
                                <td class="text-end fw-bold fs-5 text-primary">${{ number_format($service->total_cost, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Authority Commission Notice --}}
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-shield-check fs-3 me-3 text-info"></i>
                    <div class="small">
                        <strong>Official Guarantee & Transparent Fee Breakdown:</strong>
                        Your payment is secured through the official portal. Pursuant to platform regulations, <strong>2.5% (${{ number_format($service->total_cost * 0.025, 2) }})</strong> of this transaction is allocated as authority maintenance commission, and <strong>97.5% (${{ number_format($service->total_cost * 0.975, 2) }})</strong> is disbursed to {{ $service->workshop->name }}.
                    </div>
                </div>

                {{-- Payment Form --}}
                <form method="POST" action="{{ route('payments.process', $service) }}">
                    @csrf
                    <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2 text-success"></i>Select Payment Method</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="form-check custom-option border rounded p-3 h-100">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="card" checked>
                                <label class="form-check-label w-100 cursor-pointer" for="pay_card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="d-block"><i class="bi bi-credit-card me-1 text-primary"></i>Credit / Debit Card</strong>
                                        <i class="bi bi-check-circle-fill text-primary"></i>
                                    </div>
                                    <small class="text-muted d-block mt-1">Visa, Mastercard, American Express</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check custom-option border rounded p-3 h-100">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_bkash" value="bkash">
                                <label class="form-check-label w-100 cursor-pointer" for="pay_bkash">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="d-block"><i class="bi bi-phone me-1 text-danger"></i>bKash / Mobile Wallet</strong>
                                    </div>
                                    <small class="text-muted d-block mt-1">Instant mobile financial payment</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check custom-option border rounded p-3 h-100">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_mobile" value="mobile_banking">
                                <label class="form-check-label w-100 cursor-pointer" for="pay_mobile">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="d-block"><i class="bi bi-device-ssd me-1 text-warning"></i>Nagad / Rocket</strong>
                                    </div>
                                    <small class="text-muted d-block mt-1">Direct digital wallet checkout</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check custom-option border rounded p-3 h-100">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="bank_transfer">
                                <label class="form-check-label w-100 cursor-pointer" for="pay_bank">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="d-block"><i class="bi bi-bank me-1 text-secondary"></i>Direct Bank Transfer</strong>
                                    </div>
                                    <small class="text-muted d-block mt-1">Electronic bank clearing</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg py-3 fw-bold">
                            <i class="bi bi-lock-fill me-2"></i>Pay ${{ number_format($service->total_cost, 2) }} Now
                        </button>
                        <a href="{{ route('services.show', $service) }}" class="btn btn-outline-secondary">Cancel and Return to Service Details</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
