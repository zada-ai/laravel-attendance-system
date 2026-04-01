@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <div class="max-w-6xl mx-auto p-6 text-black">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Leave Requests</h1>
                    <p class="text-slate-500 mt-2">Submit a new leave request or manage pending requests.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif

        @if ($user->role === 'admin')
            <div class="bg-white rounded-3xl shadow overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">All Leave Requests</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">From</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">To</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($leaveRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $request->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $request->start_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ optional($request->end_date)->format('Y-m-d') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ \Illuminate\Support\Str::limit($request->reason, 60) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $request->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 space-y-2">
                                        <form method="POST" action="{{ route('leave.update', $request) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="sr-only" for="status-{{ $request->id }}">Status</label>
                                                <select id="status-{{ $request->id }}" name="status" class="w-full rounded-2xl border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="rejected" {{ $request->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </div>
                                            <div>
                                                <textarea name="admin_comment" rows="2" placeholder="Add a comment (optional)" class="w-full rounded-2xl border border-slate-300 px-3 py-2">{{ $request->admin_comment }}</textarea>
                                            </div>
                                            <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">Update Status</button>
                                        </form>
                                        @if ($request->admin_comment && $request->status !== 'pending')
                                            <div class="rounded-2xl bg-slate-100 p-3 text-slate-700">
                                                <p class="text-xs uppercase tracking-wide text-slate-500">Admin comment</p>
                                                <p class="text-sm">{{ $request->admin_comment }}</p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="grid gap-6">
                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">Submit Leave Request</h2>
                    @if ($errors->any())
                        <div class="bg-rose-100 border border-rose-200 text-rose-800 rounded-3xl p-4 mb-4">
                            <ul class="space-y-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('leave.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Reason</label>
                            <textarea name="reason" rows="4" required class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Describe why you need leave..."></textarea>
                        </div>
                        <button type="submit" class="rounded-3xl bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700 transition">Submit Leave Request</button>
                    </form>
                </div>

                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">Your Leave Requests</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">From</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">To</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reason</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($leaveRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ $request->start_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ optional($request->end_date)->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-slate-700">{{ \Illuminate\Support\Str::limit($request->reason, 60) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $request->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">No leave requests submitted.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
