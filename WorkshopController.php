<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    private const SERVICE_CATEGORIES = [
        'oil_change'      => 'Oil Change',
        'tire_service'    => 'Tire Service',
        'brake_service'   => 'Brake Service',
        'engine_repair'   => 'Engine Repair',
        'electrical'      => 'Electrical',
        'ac_service'      => 'AC Service',
        'body_work'       => 'Body Work',
        'transmission'    => 'Transmission',
        'general_service' => 'General Service',
        'inspection'      => 'Vehicle Inspection',
    ];

    public function index(Request $request)
    {
        $query = Workshop::with('user')->where('status', 'active');

        if ($q = $request->input('q')) {
            $query->where(function($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('city', 'like', "%{$q}%")
                   ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $workshops = $query->latest()->paginate(12)->withQueryString();

        return view('workshops.index', compact('workshops'));
    }

    public function map()
    {
        $workshops = Workshop::where('status', 'active')->get();
        $categories = self::SERVICE_CATEGORIES;
        return view('workshops.map', compact('workshops', 'categories'));
    }

    public function apiLocations(Request $request)
    {
        $workshops = Workshop::where('status', 'active')->get()->map(function($w) {
            return [
                'id' => $w->id,
                'name' => $w->name,
                'owner_name' => $w->owner_name,
                'phone' => $w->phone,
                'email' => $w->email,
                'address' => $w->address,
                'city' => $w->city,
                'lat' => (float) ($w->latitude ?? 23.8103),
                'lng' => (float) ($w->longitude ?? 90.4125),
                'categories' => json_decode($w->service_categories, true) ?? [],
                'url' => route('workshops.show', $w->id),
                'book_url' => route('bookings.create', ['workshop_id' => $w->id]),
            ];
        });

        return response()->json($workshops);
    }

    public function create()
    {
        $categories = self::SERVICE_CATEGORIES;
        return view('workshops.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:200',
            'owner_name'          => 'required|string|max:100',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email',
            'address'             => 'required|string',
            'city'                => 'required|string|max:100',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'license_number'      => 'nullable|string|unique:workshops',
            'service_categories'  => 'required|array|min:1',
            'description'         => 'nullable|string',
        ]);

        $data['service_categories'] = json_encode($data['service_categories']);
        $data['user_id'] = auth()->id();

        $workshop = Workshop::create($data);

        AuditLog::log('created', $workshop, [], $data);

        return redirect()->route('workshops.index')
                         ->with('success', 'Workshop registered successfully!');
    }

    public function show(Workshop $workshop)
    {
        $workshop->load(['warnings.admin', 'complaints.user']);
        $services    = $workshop->services()->with('vehicle', 'booking', 'payment')->latest()->take(10)->get();
        $totalEarned = $workshop->services()->sum('total_cost');
        $categories  = self::SERVICE_CATEGORIES;

        return view('workshops.show', compact('workshop', 'services', 'totalEarned', 'categories'));
    }

    public function edit(Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);
        $categories = self::SERVICE_CATEGORIES;
        return view('workshops.edit', compact('workshop', 'categories'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);

        $data = $request->validate([
            'name'               => 'required|string|max:200',
            'owner_name'         => 'required|string|max:100',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email',
            'address'            => 'required|string',
            'city'               => 'required|string|max:100',
            'latitude'           => 'nullable|numeric|between:-90,90',
            'longitude'          => 'nullable|numeric|between:-180,180',
            'license_number'     => 'nullable|string|unique:workshops,license_number,' . $workshop->id,
            'service_categories' => 'required|array|min:1',
            'description'        => 'nullable|string',
            'status'             => 'required|in:active,inactive,suspended',
        ]);

        $data['service_categories'] = json_encode($data['service_categories']);

        $old = $workshop->toArray();
        $workshop->update($data);

        AuditLog::log('updated', $workshop, $old, $data);

        return redirect()->route('workshops.show', $workshop)
                         ->with('success', 'Workshop updated!');
    }

    public function destroy(Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);
        AuditLog::log('deleted', $workshop, $workshop->toArray());
        $workshop->delete();

        return redirect()->route('workshops.index')
                         ->with('success', 'Workshop removed.');
    }

    private function authorizeWorkshop(Workshop $workshop): void
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $workshop->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
