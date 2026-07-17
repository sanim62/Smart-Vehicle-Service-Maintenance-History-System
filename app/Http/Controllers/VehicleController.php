<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->isAdmin()
            ? Vehicle::with('user')->latest()->paginate(10)
            : auth()->user()->vehicles()->latest()->paginate(10);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'make'                => 'required|string|max:100',
            'model'               => 'required|string|max:100',
            'year'                => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'registration_number' => 'required|string|unique:vehicles',
            'chassis_number'      => 'required|string|unique:vehicles',
            'color'               => 'nullable|string|max:50',
            'fuel_type'           => 'required|in:petrol,diesel,electric,hybrid',
            'mileage'             => 'nullable|integer|min:0',
        ]);

        $data['user_id'] = auth()->id();
        $data['mileage'] = $data['mileage'] ?? 0;

        $vehicle = Vehicle::create($data);

        AuditLog::log('created', $vehicle, [], $data);

        return redirect()->route('vehicles.index')
                         ->with('success', 'Vehicle added successfully!');
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $services = $vehicle->services()
                            ->with('workshop', 'serviceParts.part')
                            ->latest('service_date')
                            ->get();

        return view('vehicles.show', compact('vehicle', 'services'));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $data = $request->validate([
            'make'                => 'required|string|max:100',
            'model'               => 'required|string|max:100',
            'year'                => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'registration_number' => 'required|string|unique:vehicles,registration_number,' . $vehicle->id,
            'chassis_number'      => 'required|string|unique:vehicles,chassis_number,' . $vehicle->id,
            'color'               => 'nullable|string|max:50',
            'fuel_type'           => 'required|in:petrol,diesel,electric,hybrid',
            'mileage'             => 'nullable|integer|min:0',
            'status'              => 'required|in:active,inactive',
        ]);

        $old = $vehicle->toArray();
        $vehicle->update($data);

        AuditLog::log('updated', $vehicle, $old, $data);

        return redirect()->route('vehicles.show', $vehicle)
                         ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        AuditLog::log('deleted', $vehicle, $vehicle->toArray());
        $vehicle->delete();

        return redirect()->route('vehicles.index')
                         ->with('success', 'Vehicle deleted.');
    }

    private function authorizeVehicle(Vehicle $vehicle): void
    {
        if (!auth()->user()->isAdmin() && $vehicle->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
}
