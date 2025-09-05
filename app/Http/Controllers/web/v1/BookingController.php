<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreBookingRequest;
use App\Http\Requests\v1\UpdateBookingRequest;
use App\Models\Timezone;
use App\Services\AvailabilityService;

/**
 * Booking Controller
 * Handles CRUD operations for bookings
 */
class BookingController extends Controller
{

    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    /**
     * Display a listing of all bookings
     */
    public function index()
    {
        $bookings = Booking::with(['eventType', 'user'])
            ->where('user_id', auth()->user()->id)
            ->latest('start_time')
            ->paginate(10);

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
            ->paginate(10);

        return view('bookings.index', compact('bookings'))->with('viewType', 'scheduled');
    }


    /**
     * Display completed bookings only
     */
    public function completed()
    {
        $bookings = Booking::with(['eventType', 'user'])
            ->where('user_id', auth()->user()->id)
            ->where('status', 'completed')
            ->latest('start_time')
            ->paginate(10);

        return view('bookings.index', compact('bookings'))->with('viewType', 'completed');
    }

    /**
     * Display cancelled bookings only
     */
    public function cancelled()
    {
        $bookings = Booking::with(['eventType', 'user'])
            ->where('user_id', auth()->user()->id)
            ->where('status', 'cancelled')
            ->latest('start_time')
            ->paginate(10);

        return view('bookings.index', compact('bookings'))->with('viewType', 'cancelled');
    }


    /**
     * Show the form for creating a new booking
     */
    // public function create()
    // {
    //     $eventTypes = EventType::where('user_id', auth()->user()->id)
    //         ->where('is_active', true)
    //         ->get();

    //         $contacts = Contact::where('user_id', auth()->user()->id)
    //             ->where('is_active', true)
    //             ->get();

    //     $timezones = Timezone::orderBy('display_name')->get();

    //     return view('bookings.create', compact('eventTypes', 'contacts', 'timezones'));
    // }

    /**
     * Store a newly created booking
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        // dd($validated);

        // ✅ Get duration from the event type
        $eventType = EventType::findOrFail($validated['event_type_id']);

        // ✅ Validate slot availability before creating
        if (!$this->availabilityService->isSlotAvailable(
            auth()->id(),
            $validated['booking_date'],
            $validated['start_time'],
            $eventType->duration
        )) {
            return back()->withErrors(['start_time' => 'Selected time slot is not available'])
                ->withInput();
        }

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

        $timezones = Timezone::orderBy('display_name')->get();


        return view('bookings.edit', compact('booking', 'eventTypes', 'timezones'));
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

        // Get duration from the event type
        $eventType = EventType::findOrFail($validated['event_type_id']);

        // Validate slot availability before updating
        if (!$this->availabilityService->isSlotAvailable(
            auth()->id(),
            $validated['booking_date'],
            $validated['start_time'],
            $eventType->duration,
            $booking->id,
        )) {
            return back()->withErrors(['start_time' => 'Selected time slot is not available'])
                ->withInput();
        }

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

    public function create()
    {
        $eventTypes = EventType::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhereHas('team', function($teamQuery) {
                  $teamQuery->whereHas('members', function($memberQuery) {
                      $memberQuery->where('user_id', auth()->id())
                               ->where('status', 'active');
                  });
              });
    })->where('is_active', true)->get();

    $contacts = Contact::where(function($query) {
        $query->where('created_by', auth()->id())
              ->orWhereHas('team', function($teamQuery) {
                  $teamQuery->whereHas('members', function($memberQuery) {
                      $memberQuery->where('user_id', auth()->id())
                               ->where('status', 'active');
                  });
              });
    })->where('is_active', true)->get();

    $timezones = Timezone::orderBy('display_name')->get();

    return view('bookings.create', compact('eventTypes', 'contacts', 'timezones'));
}

public function createWithAttendees()
{
    $eventTypes = EventType::where('allow_multiple_attendees', true)
        ->where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhereHas('team', function($teamQuery) {
                      $teamQuery->whereHas('members', function($memberQuery) {
                          $memberQuery->where('user_id', auth()->id())
                                   ->where('status', 'active');
                      });
                  });
        })->where('is_active', true)->get();

    $contacts = Contact::where(function($query) {
        $query->where('created_by', auth()->id())
              ->orWhereHas('team', function($teamQuery) {
                  $teamQuery->whereHas('members', function($memberQuery) {
                      $memberQuery->where('user_id', auth()->id())
                               ->where('status', 'active');
                  });
              });
    })->where('is_active', true)->get();

    return view('bookings.create-with-attendees', compact('eventTypes', 'contacts'));
}


    public function storeWithAttendees(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'attendees' => 'required|array|min:1',
            'attendees.*.type' => 'required|in:contact,email',
            'attendees.*.contact_id' => 'required_if:attendees.*.type,contact|exists:contacts,id',
            'attendees.*.name' => 'required_if:attendees.*.type,email|string',
            'attendees.*.email' => 'required_if:attendees.*.type,email|email',
            'attendees.*.role' => 'required|in:organizer,required,optional',
        ]);

        $eventType = EventType::findOrFail($validated['event_type_id']);

        // Check if multiple attendees are allowed
        if (!$eventType->allow_multiple_attendees && count($validated['attendees']) > 1) {
            return response()->json(['error' => 'Multiple attendees not allowed for this event type'], 422);
        }

        $booking = Booking::create([
            'event_type_id' => $validated['event_type_id'],
            'user_id' => $eventType->user_id,
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'status' => 'pending',
        ]);

        // Add attendees
        foreach ($validated['attendees'] as $attendeeData) {
            if ($attendeeData['type'] === 'contact') {
                $contact = Contact::findOrFail($attendeeData['contact_id']);
                $booking->addAttendee($contact, $attendeeData['role']);
            } else {
                // Create guest attendee
                $booking->attendees()->create([
                    'attendee_type' => 'guest',
                    'name' => $attendeeData['name'],
                    'email' => $attendeeData['email'],
                    'role' => $attendeeData['role'],
                    'status' => 'pending',
                ]);
            }
        }

        return response()->json(['data' => $booking->load('attendees')], 201);
    }
}
