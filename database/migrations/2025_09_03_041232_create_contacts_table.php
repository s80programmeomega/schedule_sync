<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Contacts represent external people who can be invited to meetings.
     * They are not team members but can participate in bookings.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Basic contact information
            $table->string('name'); // Full name
            $table->string('email')->unique(); // Email address
            $table->string('phone')->nullable(); // Phone number
            $table->string('company')->nullable(); // Company/Organization
            $table->string('job_title')->nullable(); // Job title
            $table->text('notes')->nullable(); // Internal notes about the contact

            // Contact metadata
            $table->json('custom_fields')->nullable(); // Additional custom fields
            $table->string('avatar')->nullable(); // Profile picture
            $table->string('timezone')->nullable(); // Contact's timezone

            // Relationship to team/user who created the contact
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Contact status and preferences
            $table->boolean('is_active')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);

            // Tracking
            $table->timestamp('last_contacted_at')->nullable();
            $table->integer('total_bookings')->default(0); // Cache for performance

            $table->timestamps();

            // Indexes
            $table->index(['team_id', 'is_active']);
            $table->index(['created_by', 'is_active']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};