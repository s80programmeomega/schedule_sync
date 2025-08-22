<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timezone;
use DateTimeZone;
use DateTime;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/TimezoneSeeder.php
    public function run()
    {
        $timezones = DateTimeZone::listIdentifiers();

        foreach ($timezones as $timezone) {
            $dt = new DateTime('now', new DateTimeZone($timezone));

            Timezone::updateOrCreate(
                ['name' => $timezone],
                [
                    // 'display_name' => str_replace('_', ' ', $timezone),
                    'display_name' => 'UTC' . $dt->format('P') . ' ' . str_replace('_', ' ', $timezone),

                    'offset' => $dt->format('P')
                ]
            );
        }
    }
}
