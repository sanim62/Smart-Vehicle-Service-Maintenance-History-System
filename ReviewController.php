<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use App\Models\Workshop;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        // Only the booking owner can review
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Only completed bookings can be reviewed
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed bookings.');
        }

        // One review per booking
        if ($booking->review()->exists()) {
            return redirect()->back()->with('error', 'You have already reviewed this booking.');
        }

        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'user_id'     => auth()->id(),
            'workshop_id' => $booking->workshop_id,
            'booking_id'  => $booking->id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
        ]);

        // Update the cached rating on the workshop
        $booking->workshop->updateRatingCache();

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}
