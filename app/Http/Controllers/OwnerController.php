<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Complaint;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $user       = auth()->user();
        $vehicleIds = $user->vehicles()->pluck('id');
        $vehicles   = $user->vehicles()->get();

        $data = [
            'vehicles'        => $vehicles,
            'totalVehicles'   => $vehicles->count(),
            'totalBookings'   => $user->bookings()->count(),
            'totalServices'   => Service::whereIn('vehicle_id', $vehicleIds)->count(),
            'totalSpent'      => Service::whereIn('vehicle_id', $vehicleIds)->sum('total_cost'),
            'recentBookings'  => $user->bookings()->with('vehicle', 'workshop')->latest()->take(5)->get(),
            'overdueVehicles' => $vehicles->filter(fn($v) => $v->maintenance_status === 'Overdue'),
            'dueSoonVehicles' => $vehicles->filter(fn($v) => $v->maintenance_status === 'Due Soon'),
        ];

        return view('owner.dashboard', compact('data'));
    }

    public function history(Request $request)
    {
        $user       = auth()->user();
        $vehicleIds = $user->vehicles()->pluck('id');

        $query = Service::with('vehicle', 'workshop', 'booking', 'payment')
                        ->whereIn('vehicle_id', $vehicleIds)
                        ->latest('service_date');

        if ($vid = $request->input('vehicle_id')) {
            $query->where('vehicle_id', $vid);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('service_date', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('service_date', '<=', $to);
        }

        $services = $query->paginate(15)->withQueryString();
        $vehicles = $user->vehicles()->get();

        return view('owner.history', compact('services', 'vehicles'));
    }

    public function complaints()
    {
        $user       = auth()->user();
        $complaints = Complaint::where('user_id', $user->id)
                               ->with('workshop')
                               ->latest()
                               ->paginate(15);

        return view('owner.complaints', compact('complaints'));
    }
}
