<?php

use App\Services\AttendanceAutoMarker;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-missing {date?}', function ($date = null) {
    $targetDate = $date ? Carbon::parse($date) : Carbon::yesterday();
    app(AttendanceAutoMarker::class)->markAttendanceForDateForAllStudents($targetDate);

    $this->comment("Marked missing attendance for {$targetDate->format('Y-m-d')}.");
})->purpose('Mark absent or leave for students who have no attendance record on a date');
