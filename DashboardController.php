<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $data = [
                'totalUsers'     => User::count(),
                'totalVehicles'  => Vehicle::count(),
                'totalWorkshops' => Workshop::count(),
                'totalBookings'  => Booking::count(),
                'totalServices'  => Service::count(),
                'totalRevenue'   => Service::sum('total_cost'),
                'recentBookings' => Booking::with('user', 'vehicle', 'workshop')->latest()->take(5)->get(),
                'recentServices' => Service::with('vehicle', 'workshop')->latest()->take(5)->get(),
            ];
        } elseif ($user->isWorkshop()) {
            $workshopIds = $user->workshops()->pluck('id');
            $data = [
                'myWorkshops'    => $user->workshops()->get(),
                'pendingBookings'=> Booking::whereIn('workshop_id', $workshopIds)->where('status', 'pending')->count(),
                'totalServices'  => Service::whereIn('workshop_id', $workshopIds)->count(),
                'totalRevenue'   => Service::whereIn('workshop_id', $workshopIds)->sum('total_cost'),
                'recentBookings' => Booking::with('user', 'vehicle')->whereIn('workshop_id', $workshopIds)->latest()->take(5)->get(),
                'activeWarnings' => \App\Models\Warning::whereIn('workshop_id', $workshopIds)->where('status', 'active')->latest()->get(),
            ];
        } else {
            // Owner
            $vehicleIds = $user->vehicles()->pluck('id');
            $vehicles = $user->vehicles()->get();

            $data = [
                'vehicles'       => $vehicles,
                'totalVehicles'  => $vehicles->count(),
                'totalBookings'  => $user->bookings()->count(),
                'totalServices'  => Service::whereIn('vehicle_id', $vehicleIds)->count(),
                'totalSpent'     => Service::whereIn('vehicle_id', $vehicleIds)->sum('total_cost'),
                'recentBookings' => $user->bookings()->with('vehicle', 'workshop')->latest()->take(5)->get(),
                'overdueVehicles'=> $vehicles->filter(fn($v) => $v->maintenance_status === 'Overdue'),
                'dueSoonVehicles'=> $vehicles->filter(fn($v) => $v->maintenance_status === 'Due Soon'),
            ];
        }

        return view('dashboard', compact('data'));
    }
}
