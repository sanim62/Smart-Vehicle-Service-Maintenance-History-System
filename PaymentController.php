<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with(['service', 'workshop', 'user'])->latest();

        if ($user->role === 'owner') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'workshop') {
            $workshop = $user->workshops()->first();
            if ($workshop) {
                $query->where('workshop_id', $workshop->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $payments = $query->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function checkout(Service $service)
    {
        $service->load(['vehicle', 'workshop', 'booking', 'serviceParts.part', 'payment']);

        if ($service->payment && $service->payment->status === 'completed') {
            return redirect()->route('services.show', $service)
                             ->with('info', 'This service repairment has already been fully paid.');
        }

        return view('payments.checkout', compact('service'));
    }

    public function processPayment(Request $request, Service $service)
    {
        if ($service->payment && $service->payment->status === 'completed') {
            return redirect()->route('services.show', $service)
                             ->with('error', 'This repairment is already paid.');
        }

        $request->validate([
            'payment_method' => 'required|in:card,bkash,mobile_banking,bank_transfer',
        ]);

        $totalAmount = $service->total_cost;
        if ($totalAmount <= 0) {
            return redirect()->back()->with('error', 'Service bill total must be greater than zero to process payment.');
        }

        $commissionRate = 2.50; // 2.5% authority commission
        $commissionAmount = round($totalAmount * 0.025, 2);
        $workshopAmount = round($totalAmount - $commissionAmount, 2);

        $payment = Payment::create([
            'user_id'           => auth()->id(),
            'service_id'        => $service->id,
            'workshop_id'       => $service->workshop_id,
            'total_amount'      => $totalAmount,
            'commission_rate'   => $commissionRate,
            'commission_amount' => $commissionAmount,
            'workshop_amount'   => $workshopAmount,
            'payment_method'    => $request->payment_method,
            'transaction_id'    => 'TXN-' . strtoupper(Str::random(10)),
            'status'            => 'completed',
            'paid_at'           => now(),
        ]);

        AuditLog::log('created', $payment, [], $payment->toArray());

        return redirect()->route('payments.index')
                         ->with('success', "Payment of $" . number_format($totalAmount, 2) . " successful! 2.5% authority commission processing completed.");
    }
}
