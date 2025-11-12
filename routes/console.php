<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sessions:generate')->dailyAt('00:05');

// On your production or dev server, add this to your crontab:
// * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1