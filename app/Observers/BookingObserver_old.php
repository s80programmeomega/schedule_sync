<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use App\Mail\BookingRescheduled;
use App\Models\User;


/**
 * Booking Observer
 *
 * Handles automatic email notifications when booking events occur
 * Uses Laravel's Observer pattern for clean separation of concerns
 *
 * Why Observer Pattern:
 * - Automatic triggering without controller bloat
 * - Consistent behavior across all booking operations
 * - Easy to test and maintain
 */
class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     * Sends confirmation email to attendee and notification to host
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function creating(Booking $booking): void
    {
        // Set the end time for the booking
        $this->setEndTime($booking);
    }
    /**
     * Handle the Booking "created" event.
     * Sends confirmation email to attendee and notification to host
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function created(Booking $booking): void
    {
        if ($booking->status === 'scheduled') {
            try {
                foreach ($booking->attendees as $attendee) {
                    // Send confirmation email to attendee
                    Mail::to($attendee->email)
                        ->send(new BookingConfirmation($booking,$attendee));
                    Log::info('Booking confirmation emails sent', [
                        'booking_id' => $booking->id,
                        'attendee_email' => $attendee->email,
                        'host_email' => $booking->eventType->user->email,
                    ]);
                }

                // send notification to host
                Mail::to($booking->eventType->user->email)
                    ->send(new BookingConfirmation($booking));
            } catch (\Exception $e) {
                Log::error('An error occurred while sending booking confirmation emails', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Booking "updating" event.
     * Sends appropriate emails based on what changed
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updating(Booking $booking): void
    {
        // Check if relevant fields have changed
        // And set the end time if they have
        if ($booking->isDirty('start_time') || $booking->isDirty('event_type_id') || $booking->isDirty('booking_date')) {
            $this->setEndTime($booking);
        }

        // Check if booking was cancelled
        // if ($booking->wasChanged('status') && $booking->status === 'cancelled') {
        //     $this->sendCancellationEmail($booking);
        // }

    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking): void
    {
        // Check if booking was cancelled first (highest priority)
        if ($booking->wasChanged('status') && $booking->status === 'cancelled') {

            $this->sendCancellationEmail($booking);
            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'attendee_email' => $booking->attendee_email,
                'host_email' => $booking->eventType->user->email,
            ]);
        }

        if ($booking->wasChanged(['booking_date', 'start_time', 'status'])) {
            if ($booking->status === 'scheduled') {
                // If the booking is rescheduled, send reschedule email
                $this->sendRescheduleEmail($booking);
            }
        }
    }


    /**
     * Send cancellation email
     * @param \App\Models\Booking $booking
     * @return void
     */
    private function sendCancellationEmail(Booking $booking): void
    {
        try {
            // Determine who cancelled (based on who's authenticated or other logic)
            $cancelledBy = auth()->check() && auth()->id() === $booking->eventType->user_id
                ? 'host'
                : 'attendee';

            foreach ($booking->attendees as $attendee) {
                // Send cancellation email to attendee
                Mail::to($attendee->email)
                    ->send(new BookingCancellation($booking, $cancelledBy));
            }
            // Send to attendee
            // Mail::to($booking->attendee_email)
            //     ->send(new BookingCancellation($booking, $cancelledBy));

            // Send to host
            $eventType = EventType::where('id', $booking->event_type_id)->first();
            $user = User::where('id', $eventType->user_id)->first();
            Mail::to($user->email)
                ->send(new BookingCancellation($booking, $cancelledBy));

            Log::info('Booking cancellation emails sent', [
                'booking_id' => $booking->id,
                'cancelled_by' => $cancelledBy,
            ]);
        } catch (\Exception $e) {
            Log::error('An error occurred while trying to send cancellation emails', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }



    /**
     * Send reschedule email
     */
    private function sendRescheduleEmail(Booking $booking): void
    {
        try {
            foreach ($booking->attendees as $attendee) {
                Mail::to($attendee->email)
                    ->send(new BookingRescheduled($booking));
            }

            Mail::to($booking->eventType->user->email)
                ->send(new BookingRescheduled($booking));

            Log::info('Booking reschedule emails sent', [
                'booking_id' => $booking->id,
            ]);
        } catch (\Exception $e) {
            Log::error('An error occurred while trying to send reschedule emails', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
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
        if ($booking->start_time && $booking->event_type_id) {
            $eventType = EventType::find($booking->event_type_id);

            if ($eventType) {
                $fullStartTime = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
                $booking->end_time = $fullStartTime->addMinutes($eventType->duration)->format('g:i A');
            }
        }
    }
}
