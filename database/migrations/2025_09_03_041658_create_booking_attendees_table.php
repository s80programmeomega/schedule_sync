<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table manages multiple attendees for bookings,
     * supporting both team members and external contacts.
     */
    public function up(): void
    {
        Schema::create('booking_attendees', function (Blueprint $table) {
            $table->id();

            // Relationship to booking
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship for attendees
            $table->morphs('attendee'); // Creates attendee_id and attendee_type columns
            /*
             * attendee_type can be:
             * - App\Models\User (for team members)
             * - App\Models\Contact (for external contacts)
             * - App\Models\Guest (for one-time attendees, created on-the-fly)
             */

            // Attendee details (cached for performance and data integrity)
            $table->string('name'); // Attendee name
            $table->string('email'); // Attendee email
            $table->string('phone')->nullable(); // Attendee phone

            // Attendee role and status
            $table->enum('role', ['organizer', 'required', 'optional'])->default('required');
            $table->enum('status', ['pending', 'accepted', 'declined', 'tentative'])->default('pending');

            // Response tracking
            $table->timestamp('responded_at')->nullable();
            $table->text('response_notes')->nullable(); // Optional message from attendee

            // Notification preferences for this specific booking
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);

            // Reminder tracking
            $table->timestamp('reminder_24h_sent_at')->nullable();
            $table->timestamp('reminder_1h_sent_at')->nullable();

            $table->timestamps();

            // Ensure unique booking-attendee combination
            $table->unique(['booking_id', 'attendee_id', 'attendee_type'], 'unique_booking_attendee');

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index(['attendee_id', 'attendee_type']);
            $table->index('email'); // For quick lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_attendees');
    }
};
