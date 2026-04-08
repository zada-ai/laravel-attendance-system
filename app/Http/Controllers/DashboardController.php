<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\AttendanceAutoMarker;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(AttendanceAutoMarker $autoMarker)
    {
        // Fetch fresh user data from database to bypass caching
        $user = User::find(Auth::id());

        if ($user->role !== 'admin') {
            $autoMarker->markMissingAttendanceForUser($user);
        }

        if ($user->role === 'admin') {
            $studentCount = User::where('role', 'student')->count();
            $leaveRequests = LeaveRequest::with('user')->latest()->get();
            $pendingLeaveCount = $leaveRequests->where('status', 'pending')->count();
            $tasks = Task::with(['user', 'assignedBy'])->latest()->get();
            $submittedTaskCount = $tasks->where('status', 'submitted')->count();
            $recentLeaves = $leaveRequests->take(5);
            $recentTasks = $tasks->take(5);
            $todayAttendanceSummary = [
                'present' => Attendance::whereDate('date', now())->where('status', 'present')->count(),
                'absent' => Attendance::whereDate('date', now())->where('status', 'absent')->count(),
                'leave' => Attendance::whereDate('date', now())->where('status', 'leave')->count(),
            ];
            $studentSummaries = User::where('role', 'student')
                ->withCount([
                    'attendances as present_count' => fn($query) => $query->where('status', 'present'),
                    'attendances as absent_count' => fn($query) => $query->where('status', 'absent'),
                    'attendances as leave_count' => fn($query) => $query->where('status', 'leave'),
                    'leaveRequests',
                ])
                ->orderBy('name')
                ->get();

            $recentStudents = User::where('role', 'student')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.admin', compact(
                'user',
                'studentCount',
                'leaveRequests',
                'pendingLeaveCount',
                'tasks',
                'submittedTaskCount',
                'recentLeaves',
                'recentTasks',
                'todayAttendanceSummary',
                'studentSummaries',
                'recentStudents'
            ));
        }

        $todayAttendance = $user->attendances()->whereDate('date', now())->first();
        $attendanceCount = $user->attendances()->count();
        $leaveRequests = $user->leaveRequests()->latest()->get();
        $tasks = $user->tasks()->latest()->get();

        return view('dashboard.user', compact('user', 'todayAttendance', 'attendanceCount', 'leaveRequests', 'tasks'));
    }
}
