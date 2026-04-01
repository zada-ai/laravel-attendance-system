@extends('layouts.app')

@section('title', 'Attendance')

@section('content')

<div class="max-w-5xl mx-auto p-6 text-black">

    <!-- Header -->
    <div class="bg-white shadow rounded-2xl p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Attendance</h1>
        <p class="text-gray-500 mt-1">Hello, {{ $user->name }}</p>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        <div class="bg-white p-5 rounded-2xl shadow">
            <p class="text-gray-500">Today's Status</p>
            <p class="text-xl font-semibold mt-1">
                @if ($todayAttendance)
                    @if ($todayAttendance->status === 'leave')
                        Leave (L) ✅
                    @elseif ($todayAttendance->status === 'absent')
                        Absent ❌
                    @else
                        Present ✅
                    @endif
                @else
                    Not Marked ❌
                @endif
            </p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow flex items-center justify-center">

            @unless ($todayAttendance)
                @if ($approvedLeaveToday)
                    <div class="text-amber-700 font-semibold">
                        Aaj ki leave approved hai, attendance automatically marked as <span class="font-bold">L</span>.
                    </div>
                @else
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <div class="flex flex-col gap-4 w-full max-w-sm">
                            <label class="text-sm font-medium text-gray-700">Choose today's status</label>
                            <select name="status" class="rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="leave">Leave</option>
                            </select>
                            <button type="submit"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-xl shadow hover:bg-indigo-700 transition">
                                Mark Attendance
                            </button>
                        </div>
                    </form>
                @endif
            @else
                <div class="text-green-600 font-semibold">
                    You already marked attendance today 🎉
                </div>
            @endunless

        </div>

    </div>

    <!-- History -->
    <div class="bg-white shadow rounded-2xl p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4">Attendance History</h2>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">

                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Date</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3">{{ $attendance->date->format('Y-m-d') }}</td>
                            <td class="p-3">
                                @php
                            $statusClasses = [
                                'present' => 'bg-green-100 text-green-700',
                                'absent' => 'bg-rose-100 text-rose-700',
                                'leave' => 'bg-amber-100 text-amber-700',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm {{ $statusClasses[$attendance->status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $attendance->status === 'leave' ? 'L' : ucfirst($attendance->status) }}
                        </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-4 text-center text-gray-500">
                                No attendance records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6 text-right">
        <a href="{{ route('dashboard') }}"
           class="text-indigo-600 font-semibold hover:underline">
            ← Back to Dashboard
        </a>
    </div>

</div>

@endsection
