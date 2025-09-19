<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\BookingAttendee;

class AttendeeAddedToBooking
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bookingAttendee;

    public function __construct(BookingAttendee $bookingAttendee)
    {
        $this->bookingAttendee = $bookingAttendee;
    }
}
