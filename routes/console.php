<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Process recurring transactions daily at midnight
use Illuminate\Support\Facades\Schedule;

Schedule::command('transactions:process-recurring')->dailyAt('09:50');