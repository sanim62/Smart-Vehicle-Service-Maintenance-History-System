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
            return redirect()->route('admin.dashboard');
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
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        } else {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard', compact('data'));
    }
}
