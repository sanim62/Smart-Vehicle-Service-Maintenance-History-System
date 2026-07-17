<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Smart Vehicle Service & Maintenance History System
|--------------------------------------------------------------------------
| Routes are revealed phase by phase (controlled by SHOW_PHASE in .env).
| Phase 1 → welcome only. Each higher phase adds more features.
*/

$phase = (int) env('SHOW_PHASE', 1);

// ══════════════════════════════════════════════════════════════
// PHASE 1 — Welcome / Landing Page
// ══════════════════════════════════════════════════════════════
// Auto-logout any authenticated user who returns to the welcome page.
// This covers: clicking the site logo, pressing the browser back button,
// or navigating directly to / — no manual logout step required.
Route::get('/', function () {
    if (Auth::check()) {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return view('welcome');
});

// ══════════════════════════════════════════════════════════════
// PHASE 2 — Login & Registration
// ══════════════════════════════════════════════════════════════
if ($phase >= 2) {
    require __DIR__ . '/auth.php';
}

// ══════════════════════════════════════════════════════════════
// PHASE 3 — Dashboard & Profile (requires login)
// ══════════════════════════════════════════════════════════════
if ($phase >= 3) {
    Route::middleware(['auth'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Owner Portal routes
        Route::get('/owner/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
        Route::get('/owner/history', [OwnerController::class, 'history'])->name('owner.history');
        Route::get('/owner/complaints', [OwnerController::class, 'complaints'])->name('owner.complaints');

        // Booking upfront cost estimates API (AJAX)
        Route::get('/api/estimate', [BookingController::class, 'getEstimate'])->name('bookings.estimate');

        // Reviews
        Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');

        // Workshop Hours & Service Menu Estimates
        Route::get('/workshops/{workshop}/hours', [WorkshopController::class, 'hours'])->name('workshops.hours');
        Route::post('/workshops/{workshop}/hours', [WorkshopController::class, 'saveHours'])->name('workshops.hours.save');
        Route::get('/workshops/{workshop}/estimates', [WorkshopController::class, 'editEstimates'])->name('workshops.estimates');
        Route::post('/workshops/{workshop}/estimates', [WorkshopController::class, 'saveEstimates'])->name('workshops.estimates.save');

        Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    });
}

// ══════════════════════════════════════════════════════════════
// PHASE 4 — Vehicles & Workshops
// ══════════════════════════════════════════════════════════════
if ($phase >= 4) {
    Route::middleware(['auth'])->group(function () {

        // Vehicles
        Route::resource('vehicles', VehicleController::class);

        // Workshops (map route must come before resource)
        Route::get('/workshops-map',           [WorkshopController::class, 'map'])->name('workshops.map');
        Route::get('/api/workshops-locations', [WorkshopController::class, 'apiLocations'])->name('workshops.api');
        Route::resource('workshops', WorkshopController::class);

    });
}

// ══════════════════════════════════════════════════════════════
// PHASE 5 — Bookings & Services
// ══════════════════════════════════════════════════════════════
if ($phase >= 5) {
    Route::middleware(['auth'])->group(function () {

        Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
        Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

        Route::resource('services', ServiceController::class)->except(['destroy']);

        Route::resource('parts', PartController::class);
        Route::post('/parts/add-to-service',          [PartController::class, 'addToService'])->name('parts.addToService');
        Route::delete('/service-parts/{servicePart}', [PartController::class, 'removeFromService'])->name('parts.removeFromService');

    });
}

// ══════════════════════════════════════════════════════════════
// PHASE 6 — Payments, Complaints & Reports
// ══════════════════════════════════════════════════════════════
if ($phase >= 6) {
    Route::middleware(['auth'])->group(function () {

        // Payments & Checkout
        Route::get('/payments',                        [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/services/{service}/checkout',     [PaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/services/{service}/pay',         [PaymentController::class, 'processPayment'])->name('payments.process');

        // Complaints
        Route::get('/complaints/submit',  [ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/complaints/submit', [ComplaintController::class, 'store'])->name('complaints.store');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    });
}

// ══════════════════════════════════════════════════════════════
// PHASE 7 — Full Admin Panel (complete system)
// ══════════════════════════════════════════════════════════════
if ($phase >= 7) {
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard',                         [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/financials',                        [AdminController::class, 'financials'])->name('financials');

        Route::get('/users',                             [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/role',               [AdminController::class, 'updateUserRole'])->name('users.role');
        Route::delete('/users/{user}',                   [AdminController::class, 'deleteUser'])->name('users.delete');

        Route::get('/complaints',                        [AdminController::class, 'complaints'])->name('complaints');
        Route::get('/complaints/{complaint}',            [AdminController::class, 'showComplaint'])->name('complaints.show');
        Route::patch('/complaints/{complaint}/reply',    [AdminController::class, 'replyComplaint'])->name('complaints.reply');
        Route::post('/complaints/{complaint}/warning',   [AdminController::class, 'issueWarning'])->name('complaints.warning');
        Route::patch('/workshops/{workshop}/status',     [AdminController::class, 'toggleWorkshopStatus'])->name('workshops.status');
        Route::post('/workshops/{workshop}/verify',      [AdminController::class, 'verifyWorkshop'])->name('workshops.verify');

        Route::get('/audit-logs',                        [AdminController::class, 'auditLogs'])->name('auditLogs');

    });
}
