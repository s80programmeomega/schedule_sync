<?php

namespace App\Jobs;

use App\Mail\BookingReminder;
use App\Models\Booking;
use App\Models\BookingAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Send Booking Reminders Job
 *
 * Processes reminder emails for upcoming bookings
 * Runs via scheduled commands (24h and 1h before meetings)
 *
 * Why Job Pattern:
 * - Asynchronous processing for better performance
 * - Retry mechanism for failed emails
 * - Queue management for high volume
 */
class SendBookingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run
     */
    public int $timeout = 120;

    public function __construct(
        public string $reminderType  // '24h' or '1h'
    ) {}

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $bookings = $this->getBookingsForReminder();

        Log::info("Processing {$this->reminderType} reminders", [
            'count' => $bookings->count(),
            'reminder_type' => $this->reminderType,
        ]);

        if ($bookings->isEmpty()) {
            Log::info("No bookings found for {$this->reminderType} reminders");
            return;
        }

        foreach ($bookings as $booking) {
            try {
                foreach ($booking->attendees as $attendee) {
                    // Send to attendee
                    Mail::to($attendee->email)
                        ->send(new BookingReminder($booking, $this->reminderType));

                    Log::info('Reminder sent successfully', [
                        'booking_id' => $booking->id,
                        'attendee_name' => $attendee->name,
                        'attendee_email' => $attendee->email,
                        'reminder_type' => $this->reminderType,
                    ]);
                }
                // Send to host
                Mail::to($booking->eventType->user->email)
                    ->send(new BookingReminder($booking, $this->reminderType));

                // Mark reminder as sent to avoid duplicates
                $this->markReminderSent($booking);

                Log::info('Reminder sent successfully', [
                    'booking_id' => $booking->id,
                    'host' => $booking->eventType->user->email,
                    'reminder_type' => $this->reminderType,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send reminder', [
                    'booking_id' => $booking->id,
                    'host' => $booking->eventType->user->email,
                    'reminder_type' => $this->reminderType,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get bookings that need reminders
     */
    private function getBookingsForReminder()
    {
        $timeRange = $this->reminderType === '24h'
            ? [now()->addHours(23), now()->addHours(25)]
            : [now()->addMinutes(55), now()->addMinutes(65)];

        return Booking::with(['eventType.user'])
            ->where('status', 'scheduled')
            // ->whereBetween('start_time', $timeRange)
            ->whereNull($this->reminderType === '24h' ? 'reminder_24h_sent_at' : 'reminder_1h_sent_at')
            ->get();
    }

    /**
     * Mark reminder as sent
     */
    private function markReminderSent(Booking $booking): void
    {
        $field = $this->reminderType === '24h' ? 'reminder_24h_sent_at' : 'reminder_1h_sent_at';
        $booking->update([$field => now()]);
    }
}
