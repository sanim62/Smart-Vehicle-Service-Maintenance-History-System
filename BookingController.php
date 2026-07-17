<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $bookings = Booking::with('user', 'vehicle', 'workshop')->latest()->paginate(15);
        } elseif ($user->isWorkshop()) {
            $workshopIds = $user->workshops()->pluck('id');
            $bookings = Booking::with('user', 'vehicle', 'workshop')
                               ->whereIn('workshop_id', $workshopIds)
                               ->latest()->paginate(15);
        } else {
            $bookings = $user->bookings()->with('vehicle', 'workshop')->latest()->paginate(15);
        }

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $vehicles  = auth()->user()->vehicles()->where('status', 'active')->get();
        $workshops = Workshop::where('status', 'active')->get();

        return view('bookings.create', compact('vehicles', 'workshops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'          => 'required|exists:vehicles,id',
            'workshop_id'         => 'required|exists:workshops,id',
            'booking_date'        => 'required|date|after_or_equal:today',
            'booking_time'        => 'nullable|date_format:H:i',
            'service_type'        => 'required|string|max:100',
            'problem_description' => 'nullable|string',
        ]);

        // Make sure vehicle belongs to current user
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        // Store the upfront estimate if available
        $estimate = \App\Models\ServiceEstimate::where('workshop_id', $data['workshop_id'])
                                               ->where('service_type', $data['service_type'])
                                               ->first();
        if ($estimate) {
            $data['estimated_cost'] = ($estimate->min_price + $estimate->max_price) / 2;
        }

        $booking = Booking::create($data);
        AuditLog::log('created', $booking, [], $data);

        return redirect()->route('bookings.index')
                         ->with('success', 'Booking created! Awaiting workshop approval.');
    }

    // AJAX endpoint — returns price estimate for a given workshop + service type
    public function getEstimate(Request $request)
    {
        $estimate = \App\Models\ServiceEstimate::where('workshop_id', $request->workshop_id)
                                               ->where('service_type', $request->service_type)
                                               ->first();
        if (!$estimate) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'          => true,
            'min_price'      => $estimate->min_price,
            'max_price'      => $estimate->max_price,
            'duration_hours' => $estimate->duration_hours,
            'price_range'    => $estimate->priceRange(),
        ]);
    }

    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);
        $booking->load('vehicle', 'workshop', 'service.serviceParts.part');
        return view('bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,in_progress,completed,cancelled',
            'notes'  => 'nullable|string',
        ]);

        $old = $booking->toArray();
        $booking->update($data);
        AuditLog::log('updated', $booking, $old, $data);

        return redirect()->back()->with('success', 'Booking status updated!');
    }

    public function destroy(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if (in_array($booking->status, ['completed', 'in_progress'])) {
            return redirect()->back()->with('error', 'Cannot cancel a completed or in-progress booking.');
        }

        AuditLog::log('deleted', $booking, $booking->toArray());
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')
                         ->with('success', 'Booking cancelled.');
    }

    private function authorizeBooking(Booking $booking): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;

        $workshopIds = $user->workshops()->pluck('id')->toArray();

        if ($booking->user_id !== $user->id && !in_array($booking->workshop_id, $workshopIds)) {
            abort(403, 'Unauthorized');
        }
    }
}
