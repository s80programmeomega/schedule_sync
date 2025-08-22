<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Timezone;
use DateTimeZone;
use DateTime;

class TimezoneServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (Timezone::count() === 0) {
            $timezones = DateTimeZone::listIdentifiers();

            foreach ($timezones as $timezone) {
                $dt = new DateTime('now', new DateTimeZone($timezone));

                Timezone::create([
                    'name' => $timezone,
                    'display_name' => 'UTC' . $dt->format('P') . ' ' . str_replace('_', ' ', $timezone),
                    'offset' => $dt->format('P')
                ]);
            }
        }
    }
}
