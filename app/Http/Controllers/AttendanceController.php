<?php

namespace App\Http\Controllers;

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

        return back()->with('success', 'Attendance marked for today.');
    }
}
