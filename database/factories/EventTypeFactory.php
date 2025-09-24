<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Team;

class EventTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => null, // Personal event type by default
            'name' => fake()->randomElement([
                '15 Min Quick Call',
                '30 Min Meeting',
                '45 Min Discussion',
                '1 Hour Consultation',
                '90 Min Deep Dive',
                'Quick Chat',
                'Strategy Session',
                'Project Review',
                'Team Standup',
                'Client Onboarding',
                'Sales Call',
                'Product Demo',
                'Feedback Session',
                'Brainstorming Session',
                'Weekly Sync',
                'Monthly Review',
                'Quarterly Planning',
                'Annual Strategy',
                'One-on-One',
                'Performance Review',
                'Training Session',
                'Workshop',
                'Webinar',
                'Networking Call',
                'Interview',
                'Follow-up Meeting',
                'Check-in Call',
                'Planning Session',
                'Retrospective',
                'Kickoff Meeting',
                'Budget Review',
                'Roadmap Discussion',
                'Team Building Activity',
                'Customer Success Call',
                'Technical Support Session',
                'Consultation',
                'Mentorship Session',
                'Coaching Session',
                'Health Check',
                'Audit Meeting',
                'Status Update',
                'Problem Solving Session',
                'Idea Generation Session',
                'Collaboration Meeting',
                'Decision Making Meeting',
                'Alignment Meeting',
                'Vision Setting Meeting',
                'Goal Setting Meeting',
            ]),
            'description' => fake()->optional(0.7)->paragraph(),
            'duration' => fake()->randomElement([15, 30, 45, 60, 90]),
            'location_type' => fake()->randomElement(['zoom', 'google_meet', 'phone', 'whatsapp']),
            'location_details' => fake()->optional()->url(),
            'buffer_time_before' => fake()->randomElement([0, 5, 10, 15]),
            'buffer_time_after' => fake()->randomElement([0, 5, 10, 15]),
            'is_active' => fake()->boolean(85),
            'requires_confirmation' => fake()->boolean(70),
            'requires_approval' => fake()->boolean(70),
            'max_events_per_day' => fake()->optional(0.3)->numberBetween(1, 10),
            'color' => fake()->randomElement(['#5D5CDE', '#FF5733', '#33FF57', '#3357FF', '#FF33F5', '#33FFF5']),
        ];
    }

    public function teamEvent(): static
    {
        return $this->state(fn() => [
            'team_id' => Team::factory(),
        ]);
    }

    public function personal(): static
    {
        return $this->state(['team_id' => null]);
    }

    /**
     * Event type that requires approval
     */
    public function requiresApproval(): static
    {
        return $this->state(fn(array $attributes) => [
            'requires_approval' => true,
        ]);
    }

    /**
     * Event type with instant booking
     */
    public function instantBooking(): static
    {
        return $this->state(fn(array $attributes) => [
            'requires_approval' => false,
            'requires_confirmation' => false,
        ]);
    }
}
