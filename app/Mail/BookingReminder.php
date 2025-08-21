<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Booking Reminder Email
 *
 * Sent 24 hours and 1 hour before the meeting
 * Includes join links and preparation instructions
 */
class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;
    // use SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reminderType = '24h' // '24h' or '1h'
    ) {}

    public function envelope(): Envelope
    {
        $timeText = $this->reminderType === '24h' ? 'Tomorrow' : 'in 1 Hour';

        return new Envelope(
            subject: "Reminder: Meeting {$timeText} - {$this->booking->eventType->name}",
            tags: ['booking', 'reminder', $this->reminderType],
            metadata: [
                'booking_id' => $this->booking->id,
                'reminder_type' => $this->reminderType,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.reminder',
            with: [
                'booking' => $this->booking,
                'reminderType' => $this->reminderType,
                'timeUntil' => $this->booking->start_time->diffForHumans(),
                'joinLink' => $this->booking->meeting_link,
                'cancelLink' => route('public.booking.cancel', $this->booking->id),
            ]
        );
    }
}
