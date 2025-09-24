<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingAttendee;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected Booking $booking, protected BookingAttendee $bookingAttendee, protected string $attendeeNotes, protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📅 New Booking Request: {$this->booking->eventType->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.booking-request',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'attendee' => $this->bookingAttendee,
                'attendee_notes' => $this->attendeeNotes,
                'details_url' => url("/book/{$this->user->username}/pending/{$this->booking->id}"),
                // 'approveUrl' => url("/bookings/{$this->booking->id}/approve"),
                // 'rejectUrl' => url("/bookings/{$this->booking->id}/reject"),
            ]
        );
    }
}
