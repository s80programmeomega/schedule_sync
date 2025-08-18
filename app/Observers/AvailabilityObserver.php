<?php

namespace App\Observers;

use App\Models\Availability;
use Carbon\Carbon;

class AvailabilityObserver
{
    public function creating(Availability $availability): void
    {
        if ($availability->availability_date) {
            $availability->day_of_week = strtolower($availability->availability_date->format('l'));
        }
    }

    public function updating(Availability $availability): void
    {
        if ($availability->isDirty('availability_date')) {
            $availability->day_of_week = strtolower($availability->availability_date->format('l'));
        }
    }
}
