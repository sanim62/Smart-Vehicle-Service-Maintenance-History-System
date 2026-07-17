<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Workshop;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function create()
    {
        $complaints = Complaint::with('workshop')
                               ->where('user_id', auth()->id())
                               ->latest()
                               ->get();

        $workshops = Workshop::where('status', 'active')->orderBy('name')->get();

        return view('complaints.create', compact('complaints', 'workshops'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:complaint,demand,request,feedback',
            'workshop_id' => 'nullable|exists:workshops,id',
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string|max:5000',
        ]);

        Complaint::create([
            'user_id'     => auth()->id(),
            'workshop_id' => $request->workshop_id,
            'type'        => $request->type,
            'subject'     => $request->subject,
            'message'     => $request->message,
            'status'      => 'open',
        ]);

        return redirect()->route('complaints.create')
                         ->with('success', 'Your submission has been filed with the authority. We will investigate and respond shortly.');
    }
}
