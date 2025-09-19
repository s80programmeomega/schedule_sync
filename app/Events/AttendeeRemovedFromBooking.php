<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\BookingAttendee;

class AttendeeRemovedFromBooking
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $attendee;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, BookingAttendee $attendee)
    {
        $this->booking = $booking;
        $this->attendee = $attendee;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
