<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Team members represent the relationship between users and teams,
     * including their roles and permissions within the team.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Role and permissions
            $table->enum('role', ['owner', 'admin', 'member', 'viewer'])->default('member');
            /*
             * Role definitions:
             * - owner: Full control, can delete team, manage billing
             * - admin: Can manage team settings, add/remove members, manage event types
             * - member: Can create event types, manage own bookings, view team calendar
             * - viewer: Read-only access to team calendar and bookings
             */

            // Member status and settings
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->json('permissions')->nullable(); // Custom permissions override
            $table->timestamp('joined_at')->nullable(); // When they accepted the invitation
            $table->timestamp('invited_at')->useCurrent(); // When they were invited

            // Invitation details
            $table->string('invitation_token')->nullable(); // For email invitations
            $table->timestamp('invitation_expires_at')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users');

            $table->timestamps();

            // Ensure unique team-user combination
            $table->unique(['team_id', 'user_id']);

            // Indexes
            $table->index(['team_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('invitation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};