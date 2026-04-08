<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSummary;
use App\Models\LeaveRequest;
use App\Services\AttendanceAutoMarker;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(AttendanceAutoMarker $autoMarker)
    {
        $user = Auth::user();
        $autoMarker->markMissingAttendanceForUser($user);

        $attendances = $user->attendances()->latest('date')->get();
        $todayAttendance = $user->attendances()->whereDate('date', now())->first();

        $approvedLeaveToday = $user->leaveRequests()
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->orWhere(function ($q) {
                    $q->whereDate('start_date', '<=', now())
                        ->whereNull('end_date');
                });
            })
            ->exists();

        if (! $todayAttendance && $approvedLeaveToday) {
            $todayAttendance = $user->attendances()->create([
                'date' => now(),
                'status' => 'leave',
            ]);
            $attendances->prepend($todayAttendance);
        }

        return view('attendance.index', compact('user', 'attendances', 'todayAttendance', 'approvedLeaveToday'));
    }

    public function store(Request $request, WhatsAppNotifier $notifier)
    {
        $this->authorizePermission('mark-attendance');

        $user = Auth::user();

        if ($user->attendances()->whereDate('date', now())->exists()) {
            return back()->with('error', 'Aaj aapne attendance mark kar liya hai.');
        }

        $request->validate([
            'status' => ['required', 'in:present,absent,leave'],
        ]);

        $status = $request->status;
        if ($status === 'leave') {
            $hasApprovedLeave = $user->leaveRequests()
                ->where('status', 'approved')
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereDate('start_date', '<=', now())
                            ->whereDate('end_date', '>=', now());
                    })
                    ->orWhere(function ($q) {
                        $q->whereDate('start_date', '<=', now())
                            ->whereNull('end_date');
                    });
                })
                ->exists();

            if (! $hasApprovedLeave) {
                return back()->with('error', 'Aap ke paas aaj approved leave nahi hai.');
            }
        }

        $attendance = $user->attendances()->create([
            'date' => now(),
            'status' => $status,
        ]);

        $notifier->sendUser($user, 'Your attendance has been marked for today.');

        $this->updateAttendanceSummary($user);

        return back()->with('success', 'Attendance marked for today.');
    }

    protected function updateAttendanceSummary($user)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $presentCount = $user->attendances()
            ->whereBetween('date', [$start, $end])
            ->where('status', 'present')
            ->count();

        $absentCount = $user->attendances()
            ->whereBetween('date', [$start, $end])
            ->where('status', 'absent')
            ->count();

        $leaveCount = $user->attendances()
            ->whereBetween('date', [$start, $end])
            ->where('status', 'leave')
            ->count();

        $totalDays = $presentCount + $absentCount + $leaveCount;

        AttendanceSummary::updateOrCreate(
            [
                'user_id' => $user->id,
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
            ],
            [
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'total_days' => $totalDays,
                'grade' => $user->attendanceGrade($presentCount),
                'remarks' => '',
            ]
        );
    }
}
