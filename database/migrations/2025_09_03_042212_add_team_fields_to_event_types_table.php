<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add team-related fields to event types for team-wide events.
     */
    public function up(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            // Team association (nullable for backward compatibility)
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');

            // Event type settings for teams
            $table->boolean('is_team_event')->default(false); // Can multiple team members host?
            $table->boolean('allow_multiple_attendees')->default(false); // Multiple external attendees?
            $table->integer('max_attendees')->nullable(); // Maximum number of attendees

            // Assignment settings
            $table->enum('assignment_method', ['round_robin', 'manual', 'first_available'])->default('manual');
            /*
             * Assignment methods:
             * - round_robin: Automatically assign to team members in rotation
             * - manual: Manually assign to specific team members
             * - first_available: Assign to first available team member
             */

            // Add indexes
            $table->index(['team_id', 'is_active']);
            $table->index(['team_id', 'is_team_event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn([
                'team_id',
                'is_team_event',
                'allow_multiple_attendees',
                'max_attendees',
                'assignment_method'
            ]);
        });
    }
};