@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto p-6 class text-black">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Admin Dashboard</h1>
                    <p class="text-slate-500 mt-2">Manage students, attendance, leave approvals, tasks, and reports from one place.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.students') }}" class="rounded-2xl bg-indigo-600 px-5 py-3 text-white font-semibold hover:bg-indigo-700 transition">Students</a>
                    <a href="{{ route('admin.attendance') }}" class="rounded-2xl bg-sky-600 px-5 py-3 text-white font-semibold hover:bg-sky-700 transition">Attendance</a>
                    <a href="{{ route('admin.reports') }}" class="rounded-2xl bg-emerald-600 px-5 py-3 text-white font-semibold hover:bg-emerald-700 transition">Reports</a>
                    <a href="{{ route('tasks.index') }}" class="rounded-2xl bg-violet-600 px-5 py-3 text-white font-semibold hover:bg-violet-700 transition">Tasks</a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Students</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $studentCount }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Pending Leave</p>
                <p class="mt-3 text-3xl font-bold text-amber-700">{{ $pendingLeaveCount }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Total Tasks</p>
                <p class="mt-3 text-3xl font-bold text-sky-700">{{ $tasks->count() }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Submitted Tasks</p>
                <p class="mt-3 text-3xl font-bold text-emerald-700">{{ $submittedTaskCount }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Today Present</p>
                <p class="mt-3 text-3xl font-bold text-emerald-700">{{ $todayAttendanceSummary['present'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Today Absent</p>
                <p class="mt-3 text-3xl font-bold text-rose-700">{{ $todayAttendanceSummary['absent'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Today Leave</p>
                <p class="mt-3 text-3xl font-bold text-amber-700">{{ $todayAttendanceSummary['leave'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Recent Students</h2>
                        <p class="text-slate-500 mt-1">Quickly update role or delete student accounts directly from the dashboard.</p>
                    </div>
                    <a href="{{ route('admin.students') }}" class="rounded-2xl bg-indigo-600 px-4 py-3 text-white hover:bg-indigo-700 transition">Manage All Students</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($recentStudents as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ ucfirst($student->role) }}</td>
                                <td class="px-6 py-4 space-y-3">
                                    <form method="POST" action="{{ route('admin.users.updateRole', $student) }}" class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                        @csrf
                                        <select name="role" class="rounded-2xl border border-slate-300 px-3 py-2 text-sm">
                                            <option value="student" {{ $student->role === 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="admin" {{ $student->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="rounded-2xl bg-sky-600 px-4 py-2 text-white text-sm hover:bg-sky-700 transition">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-2xl bg-rose-600 px-4 py-2 text-white text-sm hover:bg-rose-700 transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Student Leave Summary</h2>
                <p class="text-slate-500 mt-1">Leave counts and attendance summary for each registered student.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Present</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Absent</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Leave</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Leave Requests</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($studentSummaries as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $student->present_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $student->absent_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $student->leave_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $student->leave_requests_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 mb-6">
            <div class="bg-white rounded-3xl shadow overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Latest Leave Requests</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">From</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($recentLeaves as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $request->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $request->start_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $request->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700">{{ \Illuminate\Support\Str::limit($request->reason, 60) }}</td>
                                    <td class="px-6 py-4 space-y-2">
                                        @if ($request->status === 'pending')
                                            <form method="POST" action="{{ route('leave.update', $request) }}" class="flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="rounded-2xl bg-emerald-600 px-3 py-2 text-white hover:bg-emerald-700 transition">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('leave.update', $request) }}" class="flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="rounded-2xl bg-rose-600 px-3 py-2 text-white hover:bg-rose-700 transition">Reject</button>
                                            </form>
                                        @else
                                            <span class="text-slate-500">No action</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No leave requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white rounded-3xl shadow overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Recent Tasks</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($recentTasks as $task)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $task->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $task->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $task->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($task->status == 'rejected' ? 'bg-rose-100 text-rose-700' : ($task->status == 'submitted' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700')) }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">No recent tasks.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
