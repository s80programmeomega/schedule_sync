<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add fields needed for public booking functionality
     *
     * Changes:
     * - Users: Add is_public field to control public visibility
     * - EventTypes: Add requires_approval field for booking approval workflow
     * - Bookings: Add approval fields for tracking approval status
     */
    public function up(): void
    {
        // Add public visibility control to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('bio')
                ->comment('Controls if user availability is publicly visible');
        });

        // Add approval workflow to event types
        Schema::table('event_types', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(true)->after('requires_confirmation')
                ->comment('Whether bookings need host approval');
        });

        // Add approval tracking to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('pending')->after('status')
                ->comment('Approval status for public bookings');
            $table->text('rejection_reason')->nullable()->after('approval_status')
                ->comment('Reason for booking rejection');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason')
                ->comment('When booking was approved');
            $table->timestamp('rejected_at')->nullable()->after('approved_at')
                ->comment('When booking was rejected');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });

        Schema::table('event_types', function (Blueprint $table) {
            $table->dropColumn('requires_approval');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'rejection_reason',
                'approved_at',
                'rejected_at'
            ]);
        });
    }
};
