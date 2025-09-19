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
 * Booking Rescheduled Email
 *
 * Sent when a booking is rescheduled by either party
 * Includes new meeting details and updated calendar invite
 */
class BookingRescheduled extends Mailable
{
    // use SerializesModels;
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public ?BookingAttendee $attendee = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Meeting Rescheduled: {$this->booking->eventType->name}",
            tags: ['booking', 'rescheduled'],
            metadata: [
                'booking_id' => $this->booking->id,
                'event_type' => $this->booking->eventType->name,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.rescheduled',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'attendee' => $this->attendee ,
                'host' => $this->booking->user,
                'newDateTime' => [
                    'date' => $this->booking->booking_date->format('l, F j, Y'),
                    'time' => \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A T'),
                    'duration' => $this->booking->eventType->duration . ' minutes',
                ],
                // 'originalDateTime' => $this->originalDateTime,
                'joinLink' => $this->booking->meeting_link,
                'cancelLink' => route('public.booking.cancel', $this->booking->id),
            ]
        );
    }

    public function attachments(): array
    {
        return [
            $this->generateUpdatedCalendarInvite(),
        ];
    }

    private function generateUpdatedCalendarInvite()
    {
        $startTime = \Carbon\Carbon::parse($this->booking->start_time)->format('Ymd\THis\Z');
        $endTime = \Carbon\Carbon::parse($this->booking->end_time)->format('Ymd\THis\Z');
        $now = now()->format('Ymd\THis\Z');

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//ScheduleSync//EN\r\n";
        $icsContent .= "METHOD:REQUEST\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:" . $this->booking->id . "@schedulesync.com\r\n";
        $icsContent .= "DTSTAMP:{$now}\r\n";
        $icsContent .= "DTSTART:{$startTime}\r\n";
        $icsContent .= "DTEND:{$endTime}\r\n";
        $icsContent .= "SUMMARY:{$this->booking->eventType->name} (RESCHEDULED)\r\n";
        $icsContent .= "DESCRIPTION:Meeting with {$this->booking->eventType->user->name} - RESCHEDULED\\n\\n";
        $icsContent .= "Join Link: {$this->booking->meeting_link}\\n\\n";
        $icsContent .= "Notes: {$this->booking->attendee_notes}\r\n";
        $icsContent .= "ORGANIZER:CN={$this->booking->eventType->user->name}:MAILTO:{$this->booking->eventType->user->email}\r\n";
        $icsContent .= "ATTENDEE:CN={$this->booking->attendee_name}:MAILTO:{$this->booking->attendee_email}\r\n";
        $icsContent .= "STATUS:CONFIRMED\r\n";
        $icsContent .= "SEQUENCE:1\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        return Attachment::fromData(fn() => $icsContent,  'meeting-rescheduled.ics')
            ->withMime('text/calendar');
    }
}
