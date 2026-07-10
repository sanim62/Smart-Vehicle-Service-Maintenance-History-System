<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use App\Models\Part;
use App\Models\ServicePart;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $services = Service::with('vehicle', 'workshop', 'booking')->latest()->paginate(15);
        } elseif ($user->isWorkshop()) {
            $workshopIds = $user->workshops()->pluck('id');
            $services = Service::with('vehicle', 'workshop', 'booking')
                               ->whereIn('workshop_id', $workshopIds)
                               ->latest()->paginate(15);
        } else {
            $vehicleIds = $user->vehicles()->pluck('id');
            $services = Service::with('vehicle', 'workshop', 'booking')
                               ->whereIn('vehicle_id', $vehicleIds)
                               ->latest()->paginate(15);
        }

        return view('services.index', compact('services'));
    }

    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking   = $bookingId ? Booking::with('vehicle', 'workshop')->findOrFail($bookingId) : null;
        $parts     = Part::orderBy('name')->get();

        return view('services.create', compact('booking', 'parts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id'        => 'required|exists:bookings,id',
            'service_date'      => 'required|date',
            'issue_description' => 'required|string',
            'repair_details'    => 'required|string',
            'labor_cost'        => 'required|numeric|min:0',
            'mileage_at_service'=> 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date|after:service_date',
            'technician_name'   => 'nullable|string|max:100',

            // Parts data
            'parts'             => 'nullable|array',
            'parts.*.part_id'   => 'required|exists:parts,id',
            'parts.*.quantity'  => 'required|integer|min:1',
            'parts.*.unit_price'=> 'required|numeric|min:0',
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        // Create the service record
        $service = Service::create([
            'booking_id'         => $booking->id,
            'vehicle_id'         => $booking->vehicle_id,
            'workshop_id'        => $booking->workshop_id,
            'service_date'       => $data['service_date'],
            'issue_description'  => $data['issue_description'],
            'repair_details'     => $data['repair_details'],
            'labor_cost'         => $data['labor_cost'],
            'mileage_at_service' => $data['mileage_at_service'] ?? null,
            'next_service_date'  => $data['next_service_date'] ?? null,
            'technician_name'    => $data['technician_name'] ?? null,
            'parts_cost'         => 0,
            'total_cost'         => $data['labor_cost'],
        ]);

        // Add parts (triggers will auto-update costs if MySQL triggers are set)
        if (!empty($data['parts'])) {
            foreach ($data['parts'] as $part) {
                $totalPrice = $part['quantity'] * $part['unit_price'];
                ServicePart::create([
                    'service_id' => $service->id,
                    'part_id'    => $part['part_id'],
                    'quantity'   => $part['quantity'],
                    'unit_price' => $part['unit_price'],
                    'total_price'=> $totalPrice,
                ]);
            }
            // Fallback recalculation (in case MySQL triggers aren't set)
            $service->recalculateCosts();
        }

        // Update vehicle mileage
        if ($data['mileage_at_service']) {
            $booking->vehicle->update(['mileage' => $data['mileage_at_service']]);
        }

        // Mark booking as completed
        $booking->update(['status' => 'completed']);

        AuditLog::log('created', $service, [], $service->toArray());

        return redirect()->route('services.show', $service)
                         ->with('success', 'Service record created successfully!');
    }

    public function show(Service $service)
    {
        $service->load('vehicle', 'workshop', 'booking', 'serviceParts.part');
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $parts = Part::orderBy('name')->get();
        $service->load('serviceParts.part');
        return view('services.edit', compact('service', 'parts'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'service_date'      => 'required|date',
            'issue_description' => 'required|string',
            'repair_details'    => 'required|string',
            'labor_cost'        => 'required|numeric|min:0',
            'mileage_at_service'=> 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date',
            'technician_name'   => 'nullable|string|max:100',
        ]);

        $old = $service->toArray();
        $service->update($data);
        $service->recalculateCosts();

        AuditLog::log('updated', $service, $old, $data);

        return redirect()->route('services.show', $service)
                         ->with('success', 'Service updated!');
    }
}
