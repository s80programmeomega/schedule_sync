<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Groups allow organizing team members and contacts into logical collections
     * for easier management and bulk operations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();

            // Basic group information
            $table->string('name'); // Group name (e.g., "Sales Team", "Developers")
            $table->text('description')->nullable(); // Group description
            $table->string('color')->default('#6366f1'); // Color for UI identification

            // Group settings
            $table->enum('type', ['team_members', 'contacts', 'mixed'])->default('team_members');
            /*
             * Type definitions:
             * - team_members: Only contains team members
             * - contacts: Only contains external contacts
             * - mixed: Can contain both team members and contacts
             */

            // Relationships
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Group status
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['team_id', 'is_active']);
            $table->index(['team_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
