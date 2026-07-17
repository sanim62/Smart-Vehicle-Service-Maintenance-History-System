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
                'rating_avg' => (float)$w->rating_avg,
                'total_reviews' => (int)$w->total_reviews,
                'is_verified' => (bool)$w->is_verified,
                'open_now' => $w->isOpenNow(),
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
            'photos.*'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data['service_categories'] = json_encode($data['service_categories']);
        $data['user_id'] = auth()->id();

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/workshops'), $filename);
                $photos[] = '/uploads/workshops/' . $filename;
            }
        }
        $data['photos'] = $photos;

        $workshop = Workshop::create($data);

        AuditLog::log('created', $workshop, [], $data);

        return redirect()->route('workshops.index')
                          ->with('success', 'Workshop registered successfully!');
    }

    public function show(Workshop $workshop)
    {
        $workshop->load(['warnings.admin', 'complaints.user', 'reviews.user', 'hours', 'estimates']);
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
            'photos.*'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data['service_categories'] = json_encode($data['service_categories']);

        $photos = $workshop->photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/workshops'), $filename);
                $photos[] = '/uploads/workshops/' . $filename;
            }
        }
        $data['photos'] = $photos;

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

    public function hours(Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);
        $hours = $workshop->hours;

        // If hours are not initialized, create empty ones
        if ($hours->isEmpty()) {
            for ($i = 0; $i < 7; $i++) {
                \App\Models\WorkshopHour::create([
                    'workshop_id' => $workshop->id,
                    'day_of_week' => $i,
                    'is_closed'   => true,
                ]);
            }
            $hours = $workshop->hours()->get();
        }

        return view('workshops.hours', compact('workshop', 'hours'));
    }

    public function saveHours(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);

        $request->validate([
            'hours' => 'required|array|size:7',
            'hours.*.open_time' => 'nullable',
            'hours.*.close_time' => 'nullable',
            'hours.*.is_closed' => 'nullable',
        ]);

        foreach ($request->hours as $day => $h) {
            \App\Models\WorkshopHour::updateOrCreate(
                ['workshop_id' => $workshop->id, 'day_of_week' => $day],
                [
                    'open_time'  => $h['open_time'] ?? null,
                    'close_time' => $h['close_time'] ?? null,
                    'is_closed'  => isset($h['is_closed']) ? (bool)$h['is_closed'] : false,
                ]
            );
        }

        return redirect()->route('workshops.show', $workshop)->with('success', 'Working hours updated successfully!');
    }

    public function editEstimates(Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);
        $categories = self::SERVICE_CATEGORIES;
        $estimates = $workshop->estimates->keyBy('service_type');

        return view('workshops.estimates', compact('workshop', 'categories', 'estimates'));
    }

    public function saveEstimates(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);

        $request->validate([
            'estimates' => 'required|array',
            'estimates.*.min_price' => 'nullable|numeric|min:0',
            'estimates.*.max_price' => 'nullable|numeric|min:0',
            'estimates.*.duration_hours' => 'nullable|numeric|min:0.5|max:72',
        ]);

        foreach ($request->estimates as $type => $est) {
            if (isset($est['min_price']) && isset($est['max_price']) && $est['min_price'] !== null && $est['max_price'] !== null) {
                \App\Models\ServiceEstimate::updateOrCreate(
                    ['workshop_id' => $workshop->id, 'service_type' => $type],
                    [
                        'min_price'      => $est['min_price'],
                        'max_price'      => $est['max_price'],
                        'duration_hours' => $est['duration_hours'] ?? 1.0,
                    ]
                );
            } else {
                \App\Models\ServiceEstimate::where('workshop_id', $workshop->id)
                                           ->where('service_type', $type)
                                           ->delete();
            }
        }

        return redirect()->route('workshops.show', $workshop)->with('success', 'Service pricing estimates updated successfully!');
    }

    private function authorizeWorkshop(Workshop $workshop): void
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $workshop->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
