<?php

namespace App\Observers;

use App\Models\BookingAttendee;
use App\Services\BookingEmailService;
use Illuminate\Support\Facades\Log;

/**
 * BookingAttendee Observer
 *
 * Handles email notifications when attendees are added/removed/updated
 * This is crucial for sending emails when attendees are added after booking creation
 */
class BookingAttendeeObserver
{
    public function __construct(
        private BookingEmailService $emailService
    ) {}

    /**
     * Handle the BookingAttendee "created" event.
     * Sends confirmation email to newly added attendee
     */
    public function created(BookingAttendee $attendee): void
    {
        // Only send email if booking is scheduled and attendee wants notifications
        if ($attendee->booking->status === 'scheduled' && $attendee->email_notifications) {
            $this->emailService->sendAttendeeConfirmation($attendee->booking, $attendee);

            Log::info('New attendee added to booking', [
                'booking_id' => $attendee->booking_id,
                'attendee_email' => $attendee->email,
                'attendee_name' => $attendee->name,
            ]);
        }
    }

    /**
     * Handle the BookingAttendee "updated" event.
     * Could send notification if status changes to accepted/declined
     */
    public function updated(BookingAttendee $attendee): void
    {
        // Log status changes for tracking
        if ($attendee->wasChanged('status')) {
            Log::info('Attendee status updated', [
                'booking_id' => $attendee->booking_id,
                'attendee_email' => $attendee->email,
                'old_status' => $attendee->getOriginal('status'),
                'new_status' => $attendee->status,
            ]);
        }
    }

    /**
     * Handle the BookingAttendee "deleted" event.
     * Log attendee removal
     */
    public function deleted(BookingAttendee $attendee): void
    {
        Log::info('Attendee removed from booking', [
            'booking_id' => $attendee->booking_id,
            'attendee_email' => $attendee->email,
            'attendee_name' => $attendee->name,
        ]);
    }
}
