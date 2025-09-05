<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot table to manage the many-to-many relationship between
     * groups and their members (both team members and contacts).
     */
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('group_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship to support both team members and contacts
            $table->morphs('member'); // Creates member_id and member_type columns
            /*
             * member_type can be:
             * - App\Models\TeamMember (for team members)
             * - App\Models\Contact (for external contacts)
             */

            // Member role within the group (optional)
            $table->string('role')->nullable(); // e.g., 'lead', 'coordinator'

            // Membership details
            $table->timestamp('joined_at')->useCurrent();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // Ensure unique group-member combination
            $table->unique(['group_id', 'member_id', 'member_type']);

            // Indexes
            $table->index(['group_id']);
            $table->index(['member_id', 'member_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};