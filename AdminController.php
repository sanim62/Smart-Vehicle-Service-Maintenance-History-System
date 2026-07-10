<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\Workshop;
use App\Models\Payment;
use App\Models\Warning;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ── Admin Dashboard ────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'totalUsers'       => User::count(),
            'openComplaints'   => Complaint::where('status', 'open')->count(),
            'totalVehicles'    => Vehicle::count(),
            'totalBookings'    => Booking::count(),
            'totalProcessed'   => Payment::sum('total_amount'),
            'totalCommission'  => Payment::sum('commission_amount'),
            'totalWarnings'    => Warning::count(),
        ];

        $recentComplaints = Complaint::with(['user', 'workshop'])->latest()->take(6)->get();
        $recentUsers      = User::where('role', '!=', 'admin')->latest()->take(5)->get();
        $recentPayments   = Payment::with(['user', 'workshop'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentComplaints', 'recentUsers', 'recentPayments'));
    }

    // ── Manage Users ───────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::withCount('vehicles', 'bookings')->latest();

        if ($q = $request->input('q')) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:owner,workshop,admin']);
        $old = $user->role;
        $user->update(['role' => $request->role]);
        AuditLog::log('updated', $user, ['role' => $old], ['role' => $request->role]);
        return redirect()->back()->with('success', "Role for {$user->name} updated to " . ucfirst($request->role) . '.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own admin account.');
        }

        $name = $user->name;
        AuditLog::log('deleted', $user, $user->toArray(), []);
        $user->delete();

        return redirect()->route('admin.users')->with('success', "User \"{$name}\" has been permanently deleted.");
    }

    // ── Authority Financials & Commission Reports ─────────────
    public function financials()
    {
        $stats = [
            'totalProcessed'  => Payment::sum('total_amount'),
            'totalCommission' => Payment::sum('commission_amount'),
            'workshopPayouts' => Payment::sum('workshop_amount'),
            'totalPayments'   => Payment::count(),
        ];

        $payments = Payment::with(['user', 'workshop', 'service'])->latest()->paginate(20);

        return view('admin.financials', compact('stats', 'payments'));
    }

    // ── Complaints & Issue Resolution ──────────────────────────
    public function complaints(Request $request)
    {
        $query = Complaint::with(['user', 'workshop'])->latest();

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($q = $request->input('q')) {
            $query->where('subject', 'like', "%{$q}%");
        }

        $complaints = $query->paginate(20)->withQueryString();

        return view('admin.complaints.index', compact('complaints'));
    }

    public function showComplaint(Complaint $complaint)
    {
        $complaint->load(['user', 'workshop.warnings']);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function replyComplaint(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:5000',
            'status'      => 'required|in:in_review,resolved,closed',
        ]);

        $complaint->update([
            'admin_reply' => $request->admin_reply,
            'status'      => $request->status,
            'replied_at'  => now(),
        ]);

        return redirect()->route('admin.complaints.show', $complaint)
                         ->with('success', 'Reply sent and complaint status updated.');
    }

    public function issueWarning(Request $request, Complaint $complaint)
    {
        $request->validate([
            'subject'         => 'required|string|max:255',
            'warning_message' => 'required|string|max:5000',
            'severity'        => 'required|in:low,medium,high,critical',
        ]);

        if (!$complaint->workshop_id) {
            return redirect()->back()->with('error', 'This complaint is not associated with any specific workshop.');
        }

        $warning = Warning::create([
            'workshop_id'     => $complaint->workshop_id,
            'complaint_id'    => $complaint->id,
            'admin_id'        => auth()->id(),
            'subject'         => $request->subject,
            'warning_message' => $request->warning_message,
            'severity'        => $request->severity,
            'status'          => 'active',
        ]);

        AuditLog::log('created', $warning, [], $warning->toArray());

        return redirect()->route('admin.complaints.show', $complaint)
                         ->with('success', 'Official Warning issued to the workshop owner.');
    }

    public function toggleWorkshopStatus(Request $request, Workshop $workshop)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $oldStatus = $workshop->status;
        $workshop->update(['status' => $request->status]);

        AuditLog::log('updated', $workshop, ['status' => $oldStatus], ['status' => $request->status]);

        return redirect()->back()->with('success', "Workshop status changed to " . ucfirst($request->status) . ".");
    }

    // ── Audit Logs ─────────────────────────────────────────────
    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->paginate(30);
        return view('admin.audit_logs', compact('logs'));
    }
}
