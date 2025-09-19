<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Booking Cancellation Email
 *
 * Sent when a booking is cancelled by either party
 * Includes cancellation reason and rebooking options
 */
class BookingCancellation extends Mailable
{
    // use SerializesModels;
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public ?BookingAttendee $attendee = null,
        public string $cancelledBy = 'host' // 'attendee' or 'host'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Meeting Cancelled: {$this->booking->eventType->name}",
            tags: ['booking', 'cancellation'],
            metadata: [
                'booking_id' => $this->booking->id,
                'cancelled_by' => $this->cancelledBy,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.cancellation',
            with: [
                'booking' => $this->booking,
                'attendee' => $this->attendee,
                'cancelledBy' => $this->cancelledBy,
                'reason' => $this->booking->cancellation_reason,
                'rebookLink' => route('bookings.create', $this->booking->eventType->user->username),
            ]
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
