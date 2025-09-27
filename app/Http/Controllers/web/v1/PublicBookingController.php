<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\BookingAttendee;
use App\Models\Contact;
use App\Models\EventType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Web-based Public Booking Controller
 * Handles public booking pages and form submissions
 */
class PublicBookingController extends Controller
{
    /**
     * Show user's public booking page
     */
    public function index(string $username)
    {
        $user = User::where('username', $username)->first();

        if (!$user || !$user->allowsPublicBookings()) {
            abort(404, 'User not found or not available for public booking');
        }

        $eventTypes = $user->publicEventTypes()->get();

        return view('public.booking.index', compact('user', 'eventTypes'));
    }

    public function viewDetails(string $username, Booking $booking)
    {
        $user = User::where('username', $username)->first();

        if (!$user || $booking->user_id !== $user->id) {
            abort(404, 'Booking not found');
        }

        return view('public.booking.details', compact('booking', 'user'));
    }

    public function joinBooking(Request $request, string $username, Booking $booking)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::where('username', $username)->first();

        if (!$user || $booking->user_id !== $user->id) {
            abort(404);
        }

        $contact = Contact::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'created_by' => $user->id,
            ]
        );

        $booking->addAttendee($contact, 'optional');

        // return back()->with('success', 'You have been added to the meeting!');

        return view('public.booking.success', compact('booking', 'user'))
            ->with('eventType', $booking->eventType)
            ->with('success', 'You have been added to the meeting!')
            ->with('message', 'You are now registered as an attendee for this meeting.');
    }

    /**
     * Show time selection page
     */
    public function selectTime(Request $request, string $username, EventType $eventType)
    {
        $user = User::where('username', $username)->first();

        if (!$user ||
                $eventType->user_id !== $user->id ||
                !$eventType->isPubliclyBookable()) {
            abort(404, 'Event type not available for public booking');
        }

        // Get selected date from request or default to today
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($selectedDate);

        // Find availability for the selected date
        $availability = Availability::where('user_id', $user->id)
            ->where('availability_date', $selectedDate)
            ->where('is_available', true)
            ->first();

        $timeSlots = [];
        if ($availability) {
            // Generate time slots based on event type duration
            $timeSlots = $availability->getTimeSlots($eventType->duration);
        }

        // Get next 30 days of availability for date picker
        $availableDates = Availability::where('user_id', $user->id)
            ->where('availability_date', '>=', now()->toDateString())
            ->where('availability_date', '<=', now()->addDays(30)->toDateString())
            ->where('is_available', true)
            ->pluck('availability_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        return view('public.booking.select-time', compact(
            'user',
            'eventType',
            'availability',
            'timeSlots',
            'selectedDate',
            'availableDates'
        ));
    }

    // /**
    //  * Show time selection page
    //  */
    // public function selectTime(string $username, EventType $eventType)
    // {
    //     $user = User::where('username', $username)->first();

    //     if (!$user ||
    //         $eventType->user_id !== $user->id ||
    //         !$eventType->isPubliclyBookable()) {
    //         abort(404, 'Event type not available for public booking');
    //     }

    //     return view('public.booking.select-time', compact('user', 'eventType'));
    // }

    public function store(Request $request, string $username, EventType $eventType)
    {
        $validated = $request->validate([
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_phone' => 'nullable|string|max:20',
            'attendee_notes' => 'nullable|string|max:1000',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ]);

        $user = User::where('username', $username)->first();

        if (!$user ||
                $eventType->user_id !== $user->id ||
                !$eventType->isPubliclyBookable()) {
            return back()->withErrors(['error' => 'Event type not available for public booking']);
        }

        $bookingDate = Carbon::parse($validated['booking_date']);
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($eventType->duration);

        // Verify availability exists for the selected date
        $availability = Availability::where('user_id', $user->id)
            ->where('availability_date', $bookingDate)
            ->where('is_available', true)
            ->first();

        if (!$availability) {
            return back()->withErrors(['booking_date' => 'Selected date is not available']);
        }

        // Create full datetime for start and end
        $startDateTime = $bookingDate->copy()->setTimeFromTimeString($validated['start_time']);
        $endDateTime = $startDateTime->copy()->addMinutes($eventType->duration);

        // Verify the selected time slot is within availability hours
        $availabilityStart = $bookingDate->copy()->setTimeFromTimeString($availability->getRawOriginal('start_time'));
        $availabilityEnd = $bookingDate->copy()->setTimeFromTimeString($availability->getRawOriginal('end_time'));

        if ($startDateTime->lt($availabilityStart) || $endDateTime->gt($availabilityEnd)) {
            return back()->withErrors(['start_time' => 'Selected time is outside available hours']);
        }

        // Check for conflicts with existing bookings
        $conflict = Booking::where('user_id', $user->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('status', ['scheduled', 'pending'])
            ->whereIn('approval_status', ['approved', 'pending'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q
                        ->whereTime('start_time', '<', $endDateTime->format('H:i'))
                        ->whereTime('end_time', '>', $startDateTime->format('H:i'));
                });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'Time slot is no longer available'])->withInput();
        }

        // Create booking
        $booking = Booking::create([
            'event_type_id' => $eventType->id,
            'user_id' => $user->id,
            'booking_date' => $validated['booking_date'],
            'start_time' => $startDateTime->format('H:i'),
            'end_time' => $endDateTime->format('H:i'),
            'status' => $eventType->requires_approval ? 'pending' : 'scheduled',
            'approval_status' => $eventType->requires_approval ? 'pending' : 'approved',
        ]);

        // Create contact and attendee
        $contact = Contact::firstOrCreate(
            ['email' => $validated['attendee_email']],
            [
                'name' => $validated['attendee_name'],
                'phone' => $validated['attendee_phone'] ?? null,
                'created_by' => $user->id,
            ]
        );


        $attendee = BookingAttendee::create([
            'booking_id' => $booking->id,
            'name' => $validated['attendee_name'],
            'email' => $validated['attendee_email'],
            'phone' => $validated['attendee_phone'] ?? null,
            'attendee_type' => 'contact',
            'attendee_id' => $contact->id,
        ]);

        $booking->addAttendee($attendee);

        // dd($eventType->requires_approval);

        // Send appropriate emails
        if (!$eventType->requires_approval) {
            $booking->update(['approved_at' => now()]);
            try {
                Mail::to($validated['attendee_email'])->send(new BookingApproved($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
            }
        } else {
            try {
                Mail::to($user->email)->send(new \App\Mail\BookingRequest($booking, $attendee, $validated['attendee_notes'] ?? '', $user));
                Log::info('Booking request email sent to: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send booking request email: ' . $e->getMessage());
            }
        }

        return view('public.booking.success', compact('booking', 'eventType'))
            ->with('emailSent', true)
            ->with('message', $eventType->requires_approval
                ? 'Your booking request has been sent to ' . $user->name . ' for approval.'
                : 'Booking confirmed! Check your email for details.');

        // return view('public.booking.success', compact('booking', 'eventType'));
    }

    /**
     * Store booking request
     */
    // public function store(Request $request, string $username, EventType $eventType)
    // {
    //     $validated = $request->validate([
    //         'attendee_name' => 'required|string|max:255',
    //         'attendee_email' => 'required|email|max:255',
    //         'attendee_phone' => 'nullable|string|max:20',
    //         'attendee_notes' => 'nullable|string|max:1000',
    //         'booking_date' => 'required|date|after_or_equal:today',
    //         'start_time' => 'required',
    //     ]);
    //     $user = User::where('username', $username)->first();
    //     if (!$user ||
    //             $eventType->user_id !== $user->id ||
    //             !$eventType->isPubliclyBookable()) {
    //         return back()->withErrors(['error' => 'Event type not available for public booking']);
    //     }
    //     $startTime = Carbon::parse($validated['start_time']);
    //     $endTime = $startTime->copy()->addMinutes($eventType->duration);
    //     // Check for conflicts
    //     $conflict = Booking::where('user_id', $user->id)
    //         ->whereIn('status', ['scheduled', 'pending'])
    //         ->whereIn('approval_status', ['approved', 'pending'])
    //         ->where(function ($query) use ($startTime, $endTime) {
    //             $query
    //                 ->whereBetween('start_time', [$startTime, $endTime])
    //                 ->orWhereBetween('end_time', [$startTime, $endTime]);
    //         })
    //         ->exists();
    //     if ($conflict) {
    //         return back()->withErrors(['start_time' => 'Time slot is no longer available']);
    //     }
    //     // Create booking
    //     $booking = Booking::create([
    //         'event_type_id' => $eventType->id,
    //         'user_id' => $user->id,
    //         'booking_date' => $validated['booking_date'],
    //         'start_time' => $startTime->format('H:i'),
    //         'end_time' => $endTime->format('H:i'),
    //         'status' => $eventType->requires_approval ? 'pending' : 'scheduled',
    //         'approval_status' => $eventType->requires_approval ? 'pending' : 'approved',
    //     ]);
    //     $attendee_notes = $validated['attendee_notes'];
    //     $contact = Contact::create([
    //         'user_id' => $user->id,
    //         'name' => $validated['attendee_name'],
    //         'email' => $validated['attendee_email'],
    //         'phone' => $validated['attendee_phone'] ?? null,
    //         'created_by' => $user->id,
    //     ]);
    //     $attendee = BookingAttendee::create([
    //         'booking_id' => $booking->id,
    //         'name' => $validated['attendee_name'],
    //         'email' => $validated['attendee_email'],
    //         'phone' => $validated['attendee_phone'] ?? null,
    //         'attendee_type' => 'contact',
    //         'attendee_id' => $contact->id,
    //     ]);
    //     $booking->addAttendee($attendee);
    //     if (!$eventType->requires_approval) {
    //         $booking->update(['approved_at' => now()]);
    //         Mail::to($validated['attendee_email'])->send(new BookingApproved($booking));
    //     } else {
    //         Mail::to($user->email)->send(new \App\Mail\BookingRequest($booking, $attendee, $attendee_notes, $user));
    //     }
    //     return view('public.booking.success', compact('booking', 'eventType'));
    // }
    public function showDetails(string $username, Booking $booking)
    {
        $user = User::where('username', $username)->first();

        if (!$user || $booking->user_id !== $user->id) {
            abort(404, 'Booking not found');
        }

        return view('bookings.show', compact('booking', 'user'));
    }

    /**
     * Approve booking (for hosts)
     */
    public function approve(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPendingApproval()) {
            return back()->withErrors(['error' => 'Booking is not pending approval']);
        }

        $attendee = $booking->attendees()->latest()->first();

        $booking->approve();
        Mail::to($attendee->email)->send(new BookingApproved($booking));

        return back()->with('success', 'Booking approved successfully');
    }

    /**
     * Reject booking (for hosts)
     */
    public function reject(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPendingApproval()) {
            return back()->withErrors(['error' => 'Booking is not pending approval']);
        }
        $attendee = $booking->attendees()->latest()->first();

        $booking->reject($validated['rejection_reason']);
        Mail::to($attendee->email)->send(new BookingRejected($booking));

        return back()->with('success', 'Booking rejected successfully');
    }
}
