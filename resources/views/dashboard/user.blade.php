@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Student Dashboard</h1>
                    <p class="text-slate-500 mt-2">Welcome back, {{ $user->name }}. Track attendance, submit leave, and manage your tasks.</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-rose-100 border border-rose-200 text-rose-800 px-6 py-4 rounded-3xl mb-6">{{ session('error') }}</div>
        @endif
        @if (! $user->whatsapp_number)
            <div class="bg-yellow-100 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-3xl mb-6">
                <p class="font-semibold">WhatsApp number missing.</p>
                <p class="mt-2">To receive attendance, leave, and task notifications, please add your WhatsApp number in your profile.</p>
                <a href="{{ route('profile.edit') }}" class="inline-flex mt-3 rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Update Profile</a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-3xl shadow p-6">
                <p class="text-sm text-slate-500">Role</p>
                <p class="mt-3 text-2xl font-bold text-slate-900">Student</p>
            </div>
            <div class="bg-white rounded-3xl shadow p-6">
                <p class="text-sm text-slate-500">Today's Attendance</p>
                <p class="mt-3 text-2xl font-bold text-indigo-700">{{ $todayAttendance ? 'Marked ✅' : 'Not Marked ❌' }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow p-6">
                <p class="text-sm text-slate-500">Attendance Records</p>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ $attendanceCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('attendance.index') }}" class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-center hover:border-indigo-500 transition">
                    <p class="text-sm text-slate-500">Attendance</p>
                    <p class="mt-3 text-lg font-semibold text-indigo-700">Mark Now</p>
                </a>
                <a href="{{ route('leave.index') }}" class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-center hover:border-emerald-500 transition">
                    <p class="text-sm text-slate-500">Leave</p>
                    <p class="mt-3 text-lg font-semibold text-emerald-700">Request</p>
                </a>
                <a href="{{ route('tasks.index') }}" class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-center hover:border-sky-500 transition">
                    <p class="text-sm text-slate-500">Tasks</p>
                    <p class="mt-3 text-lg font-semibold text-sky-700">View</p>
                </a>
                <a href="{{ route('profile.edit') }}" class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-center hover:border-slate-800 transition">
                    <p class="text-sm text-slate-500">Profile</p>
                    <p class="mt-3 text-lg font-semibold text-slate-900">Manage</p>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-3xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-slate-900">Recent Leave Requests</h2>
                    <a href="{{ route('leave.index') }}" class="text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse ($leaveRequests->take(3) as $request)
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-slate-900 font-semibold">{{ $request->start_date->format('Y-m-d') }} → {{ optional($request->end_date)->format('Y-m-d') ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($request->reason, 80) }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $request->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">No leave requests available.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-slate-900">Task Summary</h2>
                    <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:underline">View All</a>
                </div>
                <div class="grid gap-4">
                    <div class="rounded-3xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Total Tasks</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ $tasks->count() }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Pending Tasks</p>
                        <p class="mt-3 text-3xl font-bold text-indigo-600">{{ $tasks->whereIn('status', ['pending', 'rejected'])->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection