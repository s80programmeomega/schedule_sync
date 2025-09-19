<?php

namespace App\Listeners;

use App\Events\AttendeeRemovedFromBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\BookingCancellation;
use Illuminate\Support\Facades\Mail;

class SendAttendeeCancellationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttendeeRemovedFromBooking $event): void
    {
        $booking = $event->booking;
        $attendee = $event->attendee;

        Mail::to($attendee->email)->send(new BookingCancellation($booking, $attendee,cancelledBy: $booking->user->username));
    }
}
