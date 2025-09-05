<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Teams represent organizations or groups of users working together.
     * Similar to Calendly's team workspace concept.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();

            // Basic team information
            $table->string('name'); // Team/Organization name
            $table->string('slug')->unique(); // URL-friendly identifier (e.g., acme-corp)
            $table->text('description')->nullable(); // Team description
            $table->string('logo')->nullable(); // Team logo/avatar

            // Team settings
            $table->string('timezone')->default('UTC'); // Default team timezone
            $table->json('settings')->nullable(); // Additional team settings (JSON)

            // Ownership and status
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Team owner
            $table->boolean('is_active')->default(true); // Team status

            // Subscription/billing related (for future use)
            $table->string('plan')->default('free'); // Team plan (free, pro, enterprise)
            $table->integer('max_members')->default(5); // Maximum allowed members

            $table->timestamps();

            // Indexes for performance
            $table->index(['slug', 'is_active']);
            $table->index('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};