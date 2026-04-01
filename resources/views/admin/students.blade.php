@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
    <div class="max-w-7xl mx-auto p-6 text-black">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Student Management</h1>
                    <p class="text-slate-500 mt-2">View all registered users, attendance summaries, leave counts, and assign roles.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Registered Users</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">WhatsApp</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Present</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Absent</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Leave</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->whatsapp_number ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->roleLabel() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->present_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->absent_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $user->leave_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ App\Models\User::attendanceGrade($user->present_count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="flex gap-2 items-center">
                                        @csrf
                                        <select name="role" class="rounded-2xl border border-slate-300 px-3 py-2 text-sm">
                                            @foreach ($roles as $key => $label)
                                                <option value="{{ $key }}" @if($user->role === $key) selected @endif>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-2 text-white text-sm hover:bg-indigo-700 transition">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
