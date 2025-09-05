<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add team-related fields to the existing users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Default team for the user (they can be members of multiple teams)
            $table->foreignId('default_team_id')->nullable()->constrained('teams')->onDelete('set null');

            // User preferences
            $table->json('team_preferences')->nullable(); // Team-related preferences

            // Professional information
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('phone')->nullable();

            // Add index for performance
            $table->index('default_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_team_id']);
            $table->dropColumn([
                'default_team_id',
                'team_preferences',
                'job_title',
                'department',
                'phone'
            ]);
        });
    }
};