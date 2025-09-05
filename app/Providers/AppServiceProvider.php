<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use App\Observers\AvailabilityObserver;
use App\Models\Availability;
use App\Models\Booking;
use App\Observers\BookingObserver;
use App\Models\Contact;
use App\Models\Team;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\TeamMember;
use App\Policies\v1\TeamPolicy;
use App\Policies\v1\ContactPolicy;
use App\Policies\v1\GroupPolicy;
use App\Policies\v1\GroupMemberPolicy;
use App\Policies\v1\TeamMemberPolicy;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Timezone service provider
        // $this->app->register(TimezoneServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        Availability::observe(AvailabilityObserver::class);
        Booking::observe(BookingObserver::class);

        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(TeamMember::class, TeamMemberPolicy::class);

        Gate::policy(
            Contact::class,
            ContactPolicy::class
        );

        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(GroupMember::class, GroupMemberPolicy::class);
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}