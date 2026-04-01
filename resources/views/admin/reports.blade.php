@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Attendance Reports</h1>
                    <p class="text-slate-500 mt-2">Generate detailed attendance and leave reports for students or the entire system.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Back to Dashboard</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Generate Report</h2>
            <form method="GET" class="grid gap-4 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Student</label>
                    <select name="student_id" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3">
                        <option value="">System-wide</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if(request('student_id') == $user->id) selected @endif>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">From</label>
                    <input type="date" name="from" value="{{ request('from') ?? $from->format('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">To</label>
                    <input type="date" name="to" value="{{ request('to') ?? $to->format('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3" required>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-4 py-3 text-white font-semibold hover:bg-indigo-700 transition">Generate</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow p-6">
            <div class="grid gap-4 lg:grid-cols-4 mb-6">
                <div class="rounded-3xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Present</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-700">{{ $summary['present'] ?? 0 }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Leave</p>
                    <p class="mt-3 text-3xl font-bold text-amber-700">{{ $summary['leave'] ?? 0 }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Absent</p>
                    <p class="mt-3 text-3xl font-bold text-rose-700">{{ $summary['absent'] ?? 0 }}</p>
                </div>
                @if ($student)
                    <div class="rounded-3xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Grade</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summary['grade'] ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>

            @if ($student)
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">Student Report for {{ $student->name }}</h2>
                    <p class="text-slate-500">From {{ $from->format('Y-m-d') }} to {{ $to->format('Y-m-d') }}</p>
                </div>
            @else
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">System-wide Attendance Report</h2>
                    <p class="text-slate-500">From {{ $from->format('Y-m-d') }} to {{ $to->format('Y-m-d') }}</p>
                </div>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Present</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Leave</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Absent</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach ($studentReports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $report['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $report['present'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $report['leave'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $report['absent'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $report['grade'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
