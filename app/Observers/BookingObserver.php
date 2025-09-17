<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\EventType;
use App\Services\BookingEmailService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    public function __construct(
        private BookingEmailService $emailService
    ) {}

    public function creating(Booking $booking): void
    {
        $this->setEndTime($booking);
    }

    public function created(Booking $booking): void
    {
        if ($booking->status === 'scheduled') {
            // Load attendees to ensure they're available
            $booking->load('attendees');

            if ($booking->attendees()->exists()) {
                $this->emailService->sendConfirmationEmails($booking);
            }
        }
    }

    public function updating(Booking $booking): void
    {
        if ($booking->isDirty(['start_time', 'event_type_id', 'booking_date'])) {
            $this->setEndTime($booking);
        }
    }

    public function updated(Booking $booking): void
    {
        // Only proceed if booking has attendees
        if (!$booking->attendees()->exists()) {
            return;
        }

        $originalStatus = $booking->getOriginal('status');
        $currentStatus = $booking->status;

        // Handle cancellation
        if ($booking->wasChanged('status') && $currentStatus === 'cancelled') {
            $cancelledBy = $this->determineCancelledBy($booking);
            $this->emailService->sendCancellationEmails($booking, $cancelledBy);
            return;
        }

        // Handle reschedule scenarios
        $isReschedule = false;

        // Scenario 1: Status changed from cancelled back to scheduled
        if ($booking->wasChanged('status') && $originalStatus === 'cancelled' && $currentStatus === 'scheduled') {
            $isReschedule = true;
        }

        // Scenario 2: Status is scheduled and time/date changed
        if ($currentStatus === 'scheduled' && $booking->wasChanged(['booking_date', 'start_time'])) {
            $isReschedule = true;
        }

        if ($isReschedule) {
            $this->emailService->sendRescheduleEmails($booking);
        }
    }


    // public function updated(Booking $booking): void
    // {
    //     $originalStatus = $booking->getOriginal('status');
    //     $currentStatus = $booking->status;

    //     // Handle cancellation
    //     if ($booking->wasChanged('status') && $currentStatus === 'cancelled') {
    //         $cancelledBy = $this->determineCancelledBy($booking);
    //         $this->emailService->sendCancellationEmails($booking, $cancelledBy);
    //         return;
    //     }

    //     // Handle reschedule scenarios
    //     $isReschedule = false;

    //     // Scenario 1: Status changed from cancelled back to scheduled
    //     if ($booking->wasChanged('status') && $originalStatus === 'cancelled' && $currentStatus === 'scheduled') {
    //         $isReschedule = true;
    //     }

    //     // Scenario 2: Status is scheduled and time/date changed
    //     if ($currentStatus === 'scheduled' && $booking->wasChanged(['booking_date', 'start_time'])) {
    //         $isReschedule = true;
    //     }

    //     if ($isReschedule) {
    //         $this->emailService->sendRescheduleEmails($booking);
    //     }
    // }

    private function determineCancelledBy(Booking $booking): string
    {
        return auth()->check() && auth()->id() === $booking->eventType->user_id
            ? 'host'
            : 'attendee';
    }

    protected function setEndTime(Booking $booking): void
    {
        if (!$booking->start_time || !$booking->event_type_id) {
            return;
        }

        $eventType = $booking->eventType ?? EventType::find($booking->event_type_id);

        if ($eventType) {
            $fullStartTime = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
            $booking->end_time = $fullStartTime->addMinutes($eventType->duration)->format('H:i');
        }
    }
}
