<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;

class AttendanceAutoMarker
{
    public function markMissingAttendanceForUser(User $user): void
    {
        $lastAttendanceDate = $user->attendances()->latest('date')->value('date');
        $startDate = $lastAttendanceDate
            ? Carbon::parse($lastAttendanceDate)->addDay()
            : now()->startOfMonth();
        $endDate = now()->subDay();

        if ($startDate->gt($endDate)) {
            return;
        }

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($user->attendances()->whereDate('date', $date)->exists()) {
                continue;
            }

            $status = $this->hasApprovedLeaveForDate($user, $date) ? 'leave' : 'absent';

            $user->attendances()->create([
                'date' => $date->format('Y-m-d'),
                'status' => $status,
            ]);
        }
    }

    public function markAttendanceForDateForAllStudents(Carbon $targetDate): void
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            if ($student->attendances()->whereDate('date', $targetDate)->exists()) {
                continue;
            }

            $status = $this->hasApprovedLeaveForDate($student, $targetDate) ? 'leave' : 'absent';

            $student->attendances()->create([
                'date' => $targetDate->format('Y-m-d'),
                'status' => $status,
            ]);
        }
    }

    protected function hasApprovedLeaveForDate(User $user, Carbon $date): bool
    {
        return $user->leaveRequests()
            ->where('status', 'approved')
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->whereDate('start_date', '<=', $date)
                        ->whereDate('end_date', '>=', $date);
                })
                ->orWhere(function ($q) use ($date) {
                    $q->whereDate('start_date', '<=', $date)
                        ->whereNull('end_date');
                });
            })
            ->exists();
    }
}
