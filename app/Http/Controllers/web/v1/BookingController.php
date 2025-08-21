<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreBookingRequest;
use App\Http\Requests\v1\UpdateBookingRequest;

/**
 * Booking Controller
 * Handles CRUD operations for bookings
 */
class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index()
    {
        $bookings = Booking::with(['eventType', 'user'])
            ->where('user_id', auth()->user()->id)
            ->latest('start_time')
            ->get();

        return view('bookings.index', compact('bookings'))->with('viewType', 'all');
    }

    /**
     * Display scheduled bookings only
     */
    public function scheduled()
    {
        $bookings = Booking::with(['eventType', 'user'])
            ->where('user_id', auth()->user()->id)
            ->where('status', 'scheduled')
            ->latest('start_time')
            ->get();

        return view('bookings.index', compact('bookings'))->with('viewType', 'scheduled');
    }


    /**
     * Show the form for creating a new booking
     */
    public function create()
    {
        $eventTypes = EventType::where('user_id', auth()->user()->id)
            ->where('is_active', true)
            ->get();

        return view('bookings.create', compact('eventTypes'));
    }

    /**
     * Store a newly created booking
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $bookingData = collect($validated)->except(['full_start_time', 'full_end_time'])->all();
        $bookingData['user_id'] = auth()->id();
        $bookingData['status'] = 'scheduled';

        Booking::create($bookingData);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing a booking
     */
    public function edit(Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        $eventTypes = EventType::where('user_id', auth()->user()->id)
            ->where('is_active', true)
            ->get();

        return view('bookings.edit', compact('booking', 'eventTypes'));
    }

    /**
     * Update the specified booking
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        $validated = $request->validated();

        if ($validated['status'] === 'cancelled' && !$booking->cancelled_at) {
            $validated['cancelled_at'] = now();
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        $booking->delete();

        $redirectRoute = $request->redirect_to === 'scheduled' ? 'bookings.scheduled' : 'bookings.index';


        return redirect()->route($redirectRoute)
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Cancel the specified booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Booking cancelled successfully!');
    }
}
