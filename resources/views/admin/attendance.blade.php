@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Attendance Management</h1>
                    <p class="text-slate-500 mt-2">Add, update, or delete attendance records for any student.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-3xl shadow p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Add Attendance Record</h2>
                <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-slate-700">Student</label>
                        <select name="user_id" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Date</label>
                        <input type="date" name="date" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Status</label>
                        <select name="status" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-2xl bg-indigo-600 text-white px-4 py-3 font-semibold hover:bg-indigo-700 transition">Add Record</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Attendance Records</h2>
                </div>
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <select name="user_id" class="rounded-2xl border border-slate-300 px-4 py-3">
                        <option value="">All students</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if(request('user_id') == $user->id) selected @endif>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from" value="{{ request('from') }}" class="rounded-2xl border border-slate-300 px-4 py-3">
                    <input type="date" name="to" value="{{ request('to') }}" class="rounded-2xl border border-slate-300 px-4 py-3">
                    <button type="submit" class="rounded-2xl bg-slate-900 text-white px-4 py-3 hover:bg-slate-800 transition">Filter</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $attendance->date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $attendance->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'present' => 'bg-emerald-100 text-emerald-700',
                                            'absent' => 'bg-rose-100 text-rose-700',
                                            'leave' => 'bg-amber-100 text-amber-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$attendance->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 flex gap-2">
                                    <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="grid gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid gap-2 sm:grid-cols-2">
                                            <input type="date" name="date" value="{{ $attendance->date->format('Y-m-d') }}" class="rounded-2xl border border-slate-300 px-3 py-2">
                                            <select name="status" class="rounded-2xl border border-slate-300 px-3 py-2">
                                                <option value="present" @if($attendance->status === 'present') selected @endif>Present</option>
                                                <option value="absent" @if($attendance->status === 'absent') selected @endif>Absent</option>
                                                <option value="leave" @if($attendance->status === 'leave') selected @endif>Leave</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.attendance.destroy', $attendance) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-2xl bg-red-600 px-4 py-2 text-white hover:bg-red-700 transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6">
                {{ $attendances->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
