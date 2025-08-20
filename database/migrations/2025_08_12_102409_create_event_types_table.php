<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('event_types', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('name'); 
			$table->text('description')->nullable();
			$table->integer('duration'); // Duration in minutes
			$table->enum('location_type', ['zoom', 'google_meet', 'phone', 'whatsapp'])->default('zoom');
			$table->string('location_details')->nullable();
			$table->integer('buffer_time_before')->default(0); // Minutes before
			$table->integer('buffer_time_after')->default(0); // Minutes after
			$table->boolean('is_active')->default(true);
			$table->boolean('requires_confirmation')->default(false);
			$table->integer('max_events_per_day')->nullable();
			$table->string('color')->default('#5D5CDE'); // Calendar color
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('event_types');
	}
};
