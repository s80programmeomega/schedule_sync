<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\Availability;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;
use App\Models\BookingAttendee;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

    /**
     * Show time selection page
     */
    public function selectTime(string $username, EventType $eventType)
    {
        $user = User::where('username', $username)->first();

        if (!$user ||
            $eventType->user_id !== $user->id ||
            !$eventType->isPubliclyBookable()) {
            abort(404, 'Event type not available for public booking');
        }

        return view('public.booking.select-time', compact('user', 'eventType'));
    }

    /**
     * Store booking request
     */
    public function store(Request $request, string $username, EventType $eventType)
    {
        $validated = $request->validate([
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_phone' => 'nullable|string|max:20',
            'attendee_notes' => 'nullable|string|max:1000',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
        ]);

        $user = User::where('username', $username)->first();

        if (!$user ||
            $eventType->user_id !== $user->id ||
            !$eventType->isPubliclyBookable()) {
            return back()->withErrors(['error' => 'Event type not available for public booking']);
        }

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($eventType->duration);

        // Check for conflicts
        $conflict = Booking::where('user_id', $user->id)
            ->whereIn('status', ['scheduled', 'pending'])
            ->whereIn('approval_status', ['approved', 'pending'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'Time slot is no longer available']);
        }

        // Create booking
        $booking = Booking::create([
            'event_type_id' => $eventType->id,
            'user_id' => $user->id,
            'booking_date' => $validated['booking_date'],
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'status' => $eventType->requires_approval ? 'pending' : 'scheduled',
            'approval_status' => $eventType->requires_approval ? 'pending' : 'approved',
        ]);


        $attendee_notes = $validated['attendee_notes'];

        $contact = Contact::create([
            'user_id' => $user->id,
            'name' => $validated['attendee_name'],
            'email' => $validated['attendee_email'],
            'phone' => $validated['attendee_phone'] ?? null,
            'created_by' => $user->id,
        ]);
        $attendee = BookingAttendee::create([
            'booking_id' => $booking->id,
            'name' => $validated['attendee_name'],
            'email' => $validated['attendee_email'],
            'phone' => $validated['attendee_phone'] ?? null,
            'attendee_type' => 'contact',
            'attendee_id' => $contact->id,
        ]);

        $booking->addAttendee($attendee);

        if (!$eventType->requires_approval) {
            $booking->update(['approved_at' => now()]);
            Mail::to($validated['attendee_email'])->send(new BookingApproved($booking));
        } else{
            Mail::to($user->email)->send(new \App\Mail\BookingRequest($booking, $attendee, $attendee_notes, $user));
        }

        return view('public.booking.success', compact('booking', 'eventType'));
    }

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
