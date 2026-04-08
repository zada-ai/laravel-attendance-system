<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $action = $request->route()->getActionMethod();

            if (! $user) {
                abort(403);
            }

            if ($action === 'reports' && $user->hasPermission('view-reports')) {
                return $next($request);
            }

            if ($action === 'attendance' && $user->hasPermission('view-attendance')) {
                return $next($request);
            }

            if (! $user->hasRole('admin')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function students(Request $request)
    {
        $users = User::withCount([
                'attendances as present_count' => fn($query) => $query->where('status', 'present'),
                'attendances as absent_count' => fn($query) => $query->where('status', 'absent'),
                'attendances as leave_count' => fn($query) => $query->where('status', 'leave'),
                'leaveRequests',
            ])
            ->when($request->role, fn($query) => $query->where('role', $request->role))
            ->orderBy('name')
            ->get();

        $roles = config('roles.list');

        return view('admin.students', compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:' . implode(',', array_keys(config('roles.list')))],
        ]);

        $user->update(['role' => $request->role]);

        // Clear Spatie permission cache
        app('cache')->forget('spatie.permission.cache');

        // Refresh the user instance from database
        $user->refresh();

        // Update auth session if updating current user's role
        if (auth()->id() === $user->id) {
            auth()->setUser($user);
        }

        return back()->with('success', 'User role updated.');
    }

    public function attendance(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $users = User::orderBy('name')->get();
        } elseif ($user->hasRole('hr')) {
            $users = User::whereIn('role', ['student', 'hr'])->orderBy('name')->get();
        } elseif ($user->hasRole('teacher')) {
            $users = User::where('role', 'student')->orderBy('name')->get();
        } else {
            abort(403);
        }

        $attendances = Attendance::with('user')
            ->when($request->user_id, fn($query) => $query->where('user_id', $request->user_id))
            ->when($request->from, fn($query) => $query->whereDate('date', '>=', $request->from))
            ->when($request->to, fn($query) => $query->whereDate('date', '<=', $request->to))
            ->when(! $user->hasRole('admin') && $user->hasRole('hr'), fn($query) => $query->whereIn('user_id', User::whereIn('role', ['student', 'hr'])->pluck('id')))
            ->when(! $user->hasRole('admin') && $user->hasRole('teacher'), fn($query) => $query->whereIn('user_id', User::where('role', 'student')->pluck('id')))
            ->latest('date')
            ->paginate(15);

        return view('admin.attendance', compact('users', 'attendances'));
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'Student deleted successfully.');
    }

    public function attendanceStore(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,leave'],
        ]);

        Attendance::create($request->only(['user_id', 'date', 'status']));

        return back()->with('success', 'Attendance record added.');
    }

    public function attendanceUpdate(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => ['required', 'in:present,absent,leave'],
            'date' => ['required', 'date'],
        ]);

        $attendance->update($request->only(['date', 'status']));

        return back()->with('success', 'Attendance record updated.');
    }

    public function attendanceDestroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }

    public function reports(Request $request)
    {
        $users = User::where('role', 'student')->orderBy('name')->get();
        $from = $request->from ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->to ? Carbon::parse($request->to) : now()->endOfMonth();

        $student = null;
        $summary = [];
        $studentReports = collect();

        if ($request->student_id) {
            $student = User::find($request->student_id);
            $presentCount = Attendance::where('user_id', $student->id)
                ->whereBetween('date', [$from, $to])
                ->where('status', 'present')
                ->count();
            $leaveCount = Attendance::where('user_id', $student->id)
                ->whereBetween('date', [$from, $to])
                ->where('status', 'leave')
                ->count();
            $absentCount = Attendance::where('user_id', $student->id)
                ->whereBetween('date', [$from, $to])
                ->where('status', 'absent')
                ->count();

            $summary = [
                'present' => $presentCount,
                'leave' => $leaveCount,
                'absent' => $absentCount,
                'grade' => User::attendanceGrade($presentCount),
            ];
        } else {
            $studentReports = $users->map(function ($user) use ($from, $to) {
                $presentCount = Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$from, $to])
                    ->where('status', 'present')
                    ->count();
                $leaveCount = Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$from, $to])
                    ->where('status', 'leave')
                    ->count();
                $absentCount = Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$from, $to])
                    ->where('status', 'absent')
                    ->count();
                $grade = User::attendanceGrade($presentCount);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'present' => $presentCount,
                    'leave' => $leaveCount,
                    'absent' => $absentCount,
                    'grade' => $grade,
                ];
            });

            $presentCount = $studentReports->sum('present');
            $leaveCount = $studentReports->sum('leave');
            $absentCount = $studentReports->sum('absent');
            $studentCount = $users->count();
            $totalDays = ($from->diffInDays($to) + 1) * max($studentCount, 1);

            $summary = [
                'present' => $presentCount,
                'leave' => $leaveCount,
                'absent' => $absentCount,
                'student_count' => $studentCount,
                'total_days' => $totalDays,
            ];
        }

        return view('admin.reports', compact('users', 'student', 'from', 'to', 'summary', 'studentReports'));
    }
}
