<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

/**
 * Booking Confirmation Email
 *
 * Enhanced to handle multiple attendees properly
 * Sends personalized emails to each attendee
 */
class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Booking $booking,
        protected ?BookingAttendee $attendee = null
    ) {}

    /**
     * Get the message envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: "Booking Confirmed: {$this->booking->eventType->name}",
            tags: ['booking', 'confirmation'],
            metadata: [
                'booking_id' => (string) $this->booking->id,
                'event_type' => $this->booking->eventType->name,
                'attendee_email' => $this->attendee?->email ?? 'host',
            ],
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        // Determine if this is for an attendee or host
        $isForAttendee = $this->attendee !== null;

        return new Content(
            view: 'emails.booking.confirmation',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'attendee' => $isForAttendee ? [
                    'name' => $this->attendee->name,
                    'email' => $this->attendee->email,
                ] : null,
                'host' => $this->booking->user,
                'isForAttendee' => $isForAttendee,
                'meetingDetails' => [
                    'date' => $this->booking->booking_date->format('l, F j, Y'),
                    'time' => \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A'),
                    'duration' => $this->booking->eventType->duration . ' minutes',
                    'timezone' => $this->booking->timezone->display_name ?? 'UTC',
                ],
                'joinLink' => $this->booking->meeting_link,
                'allAttendees' => $this->booking->attendees,
            ]
        );
    }

    /**
     * Get the attachments for the message
     */
    public function attachments(): array
    {
        return [
            $this->generateCalendarInvite(),
        ];
    }

    /**
     * Generate calendar invite attachment
     * Enhanced to include all attendees
     */
    private function generateCalendarInvite(): Attachment
    {
        $startDateTime = \Carbon\Carbon::parse($this->booking->booking_date->toDateString() . ' ' . $this->booking->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->booking->booking_date->toDateString() . ' ' . $this->booking->end_time);

        $startTime = $startDateTime->format('Ymd\THis\Z');
        $endTime = $endDateTime->format('Ymd\THis\Z');
        $now = now()->format('Ymd\THis\Z');

        // Build ICS content more efficiently
        $icsLines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ScheduleSync//EN',
            'BEGIN:VEVENT',
            "UID:{$this->booking->id}@schedulesync.com",
            "DTSTAMP:{$now}",
            "DTSTART:{$startTime}",
            "DTEND:{$endTime}",
            "SUMMARY:{$this->booking->eventType->name}",
            "DESCRIPTION:Meeting with {$this->booking->eventType->user->name}",
            "ORGANIZER:CN={$this->booking->eventType->user->name}:MAILTO:{$this->booking->eventType->user->email}",
        ];

        // Add all attendees to calendar invite
        foreach ($this->booking->attendees as $attendee) {
            $icsLines[] = "ATTENDEE:CN={$attendee->name}:MAILTO:{$attendee->email}";
        }

        if ($this->booking->meeting_link) {
            $icsLines[] = "URL:{$this->booking->meeting_link}";
        }

        $icsLines[] = 'STATUS:CONFIRMED';
        $icsLines[] = 'END:VEVENT';
        $icsLines[] = 'END:VCALENDAR';

        $icsContent = implode("\r\n", $icsLines);

        return Attachment::fromData(fn() => $icsContent, 'meeting-invite.ics')
            ->withMime('text/calendar');
    }
}
