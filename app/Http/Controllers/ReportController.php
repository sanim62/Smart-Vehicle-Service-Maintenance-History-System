<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Cost per vehicle
        $costPerVehicle = Vehicle::withSum('services', 'total_cost')
                                 ->withCount('services')
                                 ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
                                 ->get();

        // Most used parts
        $topParts = DB::table('service_parts')
                      ->join('parts', 'service_parts.part_id', '=', 'parts.id')
                      ->select('parts.name', DB::raw('SUM(service_parts.quantity) as total_qty'), DB::raw('SUM(service_parts.total_price) as total_value'))
                      ->groupBy('parts.id', 'parts.name')
                      ->orderByDesc('total_qty')
                      ->take(10)
                      ->get();

        // Workshop performance
        $workshopStats = Workshop::withCount('services')
                                 ->withSum('services', 'total_cost')
                                 ->orderByDesc('services_count')
                                 ->take(10)
                                 ->get();

        // Monthly service trend
        $monthlyTrend = Service::selectRaw('YEAR(service_date) as year, MONTH(service_date) as month, COUNT(*) as total, SUM(total_cost) as revenue')
                               ->groupByRaw('YEAR(service_date), MONTH(service_date)')
                               ->orderByRaw('year DESC, month DESC')
                               ->take(12)
                               ->get();

        return view('reports.index', compact('costPerVehicle', 'topParts', 'workshopStats', 'monthlyTrend'));
    }
}
