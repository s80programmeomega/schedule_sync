<?php
// app/Mail/BookingRejected.php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Booking Rejection Email
 *
 * Sent to the requester when their booking request is rejected
 * Includes rejection reason and alternative booking options
 */
class BookingRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Booking $booking
    ) {}

    /**
     * Get the message envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: "❌ Booking Request Declined: {$this->booking->eventType->name}",
            tags: ['booking', 'approval', 'rejected'],
            metadata: [
                'booking_id' => (string) $this->booking->id,
                'event_type' => $this->booking->eventType->name,
                'host' => $this->booking->user->name,
            ],
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.rejected',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'host' => $this->booking->user,
                'rejectionReason' => $this->booking->rejection_reason,
                'requestedMeetingDetails' => [
                    'date' => $this->booking->booking_date->format('l, F j, Y'),
                    'time' => \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A'),
                    'duration' => $this->booking->eventType->duration . ' minutes',
                ],
                'bookAgainUrl' => $this->booking->user->public_booking_url,
            ]
        );
    }

    /**
     * Get the attachments for the message
     */
    public function attachments(): array
    {
        return [];
    }
}
