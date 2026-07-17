<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\ServicePart;
use App\Models\Service;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::latest()->paginate(15);
        return view('parts.index', compact('parts'));
    }

    public function create()
    {
        return view('parts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'part_number' => 'nullable|string|max:100',
            'category'    => 'nullable|string|max:100',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|in:piece,liter,kg,set',
        ]);

        Part::create($data);

        return redirect()->route('parts.index')->with('success', 'Part added!');
    }

    public function edit(Part $part)
    {
        return view('parts.edit', compact('part'));
    }

    public function update(Request $request, Part $part)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'part_number' => 'nullable|string|max:100',
            'category'    => 'nullable|string|max:100',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|in:piece,liter,kg,set',
        ]);

        $part->update($data);

        return redirect()->route('parts.index')->with('success', 'Part updated!');
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return redirect()->route('parts.index')->with('success', 'Part removed.');
    }

    // Add/remove part from a service
    public function addToService(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'part_id'    => 'required|exists:parts,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $data['total_price'] = $data['quantity'] * $data['unit_price'];

        ServicePart::create($data);

        // Fallback recalculate (triggers handle this in MySQL)
        $service = Service::find($data['service_id']);
        $service->recalculateCosts();

        return redirect()->back()->with('success', 'Part added to service!');
    }

    public function removeFromService(ServicePart $servicePart)
    {
        $service = $servicePart->service;
        $servicePart->delete();
        $service->recalculateCosts();

        return redirect()->back()->with('success', 'Part removed from service.');
    }
}
