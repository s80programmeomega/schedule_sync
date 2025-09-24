<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

/**
 * Booking Approval Email
 *
 * Sent to the requester when their booking request is approved
 * Includes meeting details and calendar invite
 */
class BookingApproved extends Mailable
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
            subject: "✅ Booking Approved: {$this->booking->eventType->name}",
            tags: ['booking', 'approval', 'approved'],
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
            view: 'emails.booking.approved',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'host' => $this->booking->user,
                'meetingDetails' => [
                    'date' => $this->booking->booking_date->format('l, F j, Y'),
                    'time' => \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A'),
                    'duration' => $this->booking->eventType->duration . ' minutes',
                    'timezone' => $this->booking->timezone->display_name ?? 'UTC',
                ],
                'joinLink' => $this->booking->meeting_link,
                'cancelUrl' => url("/book/cancel/{$this->booking->id}"),
                'rescheduleUrl' => url("/book/reschedule/{$this->booking->id}"),
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
     */
    private function generateCalendarInvite(): Attachment
    {
        $startDateTime = \Carbon\Carbon::parse($this->booking->booking_date->toDateString() . ' ' . $this->booking->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->booking->booking_date->toDateString() . ' ' . $this->booking->end_time);

        $startTime = $startDateTime->format('Ymd\THis\Z');
        $endTime = $endDateTime->format('Ymd\THis\Z');
        $now = now()->format('Ymd\THis\Z');

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
            "DESCRIPTION:Meeting with {$this->booking->user->name}",
            "ORGANIZER:CN={$this->booking->user->name}:MAILTO:{$this->booking->user->email}",
        ];

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
