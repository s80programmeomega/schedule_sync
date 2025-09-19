<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingAttendee;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use App\Mail\BookingRescheduled;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Booking Email Service
 *
 * Centralizes all booking-related email notifications
 * Handles multiple attendees and different email scenarios
 *
 * Why Service Pattern:
 * - Single responsibility for email logic
 * - Reusable across controllers and observers
 * - Easy to test and maintain
 * - Consistent email behavior
 */
class BookingEmailService
{
    /**
     * Send confirmation emails to all attendees and host
     *
     * @param Booking $booking
     * @return void
     */
    public function sendConfirmationEmails(Booking $booking): void
    {
        try {
            // Load necessary relationships to avoid N+1 queries
            $booking->load(['attendees', 'eventType.user', 'timezone']);

            // Send confirmation to each attendee
            foreach ($booking->attendees as $attendee) {
                if ($attendee->email && $attendee->email_notifications) {
                    Mail::to($attendee->email)
                        ->send(new BookingConfirmation($booking, $attendee));

                    Log::info('Booking confirmation sent to attendee', [
                        'booking_id' => $booking->id,
                        'attendee_email' => $attendee->email,
                        'attendee_name' => $attendee->name,
                    ]);
                }
            }

            // Send notification to host
            if ($booking->eventType->user->email) {
                Mail::to($booking->eventType->user->email)
                    ->send(new BookingConfirmation($booking));

                Log::info('Booking confirmation sent to host', [
                    'booking_id' => $booking->id,
                    'host_email' => $booking->eventType->user->email,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation emails', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send confirmation email to a specific attendee
     * Used when adding attendees after booking creation
     *
     * @param Booking $booking
     * @param BookingAttendee $attendee
     * @return void
     */
    public function sendAttendeeConfirmation(Booking $booking, BookingAttendee $attendee): void
    {
        try {
            // Load relationships if not already loaded
            if (!$booking->relationLoaded('eventType')) {
                $booking->load(['eventType.user', 'timezone']);
            }

            if ($attendee->email && $attendee->email_notifications) {
                Mail::to($attendee->email)
                    ->send(new BookingConfirmation($booking, $attendee));

                Log::info('Attendee confirmation email sent', [
                    'booking_id' => $booking->id,
                    'attendee_email' => $attendee->email,
                    'attendee_name' => $attendee->name,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send attendee confirmation email', [
                'booking_id' => $booking->id,
                'attendee_id' => $attendee->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send cancellation emails to all parties
     *
     * @param Booking $booking
     * @param string $cancelledBy
     * @return void
     */
    public function sendCancellationEmails(Booking $booking, string $cancelledBy = 'attendee'): void
    {
        try {
            $booking->load(['attendees', 'eventType.user']);

            // Send to all attendees
            foreach ($booking->attendees as $attendee) {
                if ($attendee->email && $attendee->email_notifications) {
                    Mail::to($attendee->email)
                        ->send(new BookingCancellation($booking, $attendee,$cancelledBy));
                }
            }

            // Send to host
            if ($booking->user->email) {
                Mail::to($booking->user->email)
                    ->send(new BookingCancellation($booking, $attendee=null, $cancelledBy));
            }

            Log::info('Booking cancellation emails sent', [
                'booking_id' => $booking->id,
                'cancelled_by' => $cancelledBy,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send cancellation emails', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send reschedule emails to all parties
     *
     * @param Booking $booking
     * @return void
     */
    public function sendRescheduleEmails(Booking $booking): void
    {
        try {
            $booking->load(['attendees', 'eventType.user']);

            // Send to all attendees
            foreach ($booking->attendees as $attendee) {
                if ($attendee->email && $attendee->email_notifications) {
                    Mail::to($attendee->email)
                        ->send(new BookingRescheduled(booking: $booking, attendee: $attendee));
                }
            }

            // Send to host
            if ($booking->eventType->user->email) {
                Mail::to($booking->eventType->user->email)
                    ->send(new BookingRescheduled($booking));
            }

            Log::info('Booking reschedule emails sent', [
                'booking_id' => $booking->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send reschedule emails', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
