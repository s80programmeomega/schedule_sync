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
 * Booking Confirmation Email
 *
 * Sent immediately after a booking is created
 * Contains meeting details, calendar invite, and join links
 *
 * Design Pattern: Mailable with Queueable trait
 * Why: Ensures emails are sent asynchronously for better performance
 */
class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the message envelope
     * Defines sender, subject, and priority
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: "Booking Confirmed: {$this->booking->eventType->name}",
            tags: ['booking', 'confirmation'],
            metadata: [
                'booking_id' => $this->booking->id,
                'event_type' => $this->booking->eventType->name,
            ],
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.confirmation',
            with: [
                'booking' => $this->booking,
                'eventType' => $this->booking->eventType,
                'attendee' => [
                    'name' => $this->booking->attendee_name,
                    'email' => $this->booking->attendee_email,
                ],
                'host' => $this->booking->eventType->user,
                'meetingDetails' => [
                    'date' => $this->booking->booking_date->format('l, F j, Y'),
                    'time' => \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A T'),
                    'duration' => $this->booking->eventType->duration . ' minutes',
                    'timezone' => \Carbon\Carbon::parse($this->booking->start_time)->timezone->getName(),
                ],
                'joinLink' => $this->booking->meeting_link,
                'cancelLink' => route('public.booking.cancel', $this->booking->id),
                'rescheduleLink' => route('public.booking.reschedule', $this->booking->id),
            ]
        );
    }

    /**
     * Get the attachments for the message
     * Includes calendar invite (.ics file)
     */
    public function attachments(): array
    {
        return [
            $this->generateCalendarInvite(),
        ];
    }

    /**
     * Generate calendar invite attachment
     * Creates .ics file for calendar integration
     */
    private function generateCalendarInvite()
    {
        $startTime = \Carbon\Carbon::parse($this->booking->start_time)->format('Ymd\THis\Z');
        $endTime = \Carbon\Carbon::parse($this->booking->end_time)->format('Ymd\THis\Z');
        $now = now()->format('Ymd\THis\Z');

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//ScheduleSync//EN\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:" . $this->booking->id . "@schedulesync.com\r\n";
        $icsContent .= "DTSTAMP:{$now}\r\n";
        $icsContent .= "DTSTART:{$startTime}\r\n";
        $icsContent .= "DTEND:{$endTime}\r\n";
        $icsContent .= "SUMMARY:{$this->booking->eventType->name}\r\n";
        $icsContent .= "DESCRIPTION:Meeting with {$this->booking->eventType->user->name}\\n\\n";
        $icsContent .= "Join Link: {$this->booking->meeting_link}\\n\\n";
        $icsContent .= "Notes: {$this->booking->attendee_notes}\r\n";
        $icsContent .= "ORGANIZER:CN={$this->booking->eventType->user->name}:MAILTO:{$this->booking->eventType->user->email}\r\n";
        $icsContent .= "ATTENDEE:CN={$this->booking->attendee_name}:MAILTO:{$this->booking->attendee_email}\r\n";
        $icsContent .= "STATUS:CONFIRMED\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        return Attachment::fromData(fn() => $icsContent, 'meeting-invite.ics')
            ->withMime('text/calendar');

        // return [
        //     'data' => $icsContent,
        //     'name' => 'meeting-invite.ics',
        //     'options' => [
        //         'mime' => 'text/calendar',
        //     ],
        // ];
    }
}
