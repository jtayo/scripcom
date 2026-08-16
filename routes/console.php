<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tolclin:sync-routers')->everyFiveMinutes()->withoutOverlapping();

Schedule::command('routers:check')->everyFiveMinutes()->withoutOverlapping();

Schedule::command('billing:mark-overdue')->dailyAt('00:15')->withoutOverlapping();
