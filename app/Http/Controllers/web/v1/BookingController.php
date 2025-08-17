<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_notes' => 'nullable|string|max:1000',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url|max:255',
        ]);

        $validated['user_id'] = auth()->user()->id;
        $validated['status'] = 'scheduled';

        Booking::create($validated);

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
    public function update(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_notes' => 'nullable|string|max:1000',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'meeting_link' => 'nullable|url|max:255',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

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
