<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Support\Carbon;

class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function creating(Booking $booking): void
    {
        $this->setEndTime($booking);
    }

    /**
     * Handle the Booking "updating" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updating(Booking $booking): void
    {
        if ($booking->isDirty('start_time') || $booking->isDirty('event_type_id') || $booking->isDirty('booking_date')) {
            $this->setEndTime($booking);
        }
    }

    /**
     * Set the end time for the booking based on event type duration.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    protected function setEndTime(Booking $booking): void
    {
        if ($booking->booking_date && $booking->start_time && $booking->event_type_id) {
            $eventType = EventType::find($booking->event_type_id);

            if ($eventType) {
                $fullStartTime = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
                $booking->end_time = $fullStartTime->addMinutes($eventType->duration)->format('g:i A');
            }
        }
    }
}
