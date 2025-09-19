<?php


namespace App\Listeners;

use App\Events\AttendeeAddedToBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Services\BookingEmailService;

class SendAttendeeInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue;



    /**
     * Handle the event.
     */
    public function handle(AttendeeAddedToBooking $event)
    {
        $attendee = $event->bookingAttendee;

        app(bookingEmailService::class)->sendAttendeeConfirmation(booking: $attendee->booking, attendee: $attendee);
        // Mail::to($attendee->email)->send(new BookingConfirmation($attendee->booking,$attendee));
    }
}

