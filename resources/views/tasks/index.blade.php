@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
    <div class="max-w-6xl mx-auto p-6 text-black">
        <div class="bg-white rounded-3xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Tasks</h1>
                    <p class="text-slate-500 mt-1">Manage tasks, submit responses, and review feedback.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 text-emerald-800 px-5 py-4 rounded-2xl mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-rose-100 text-rose-800 px-5 py-4 rounded-2xl mb-4">{{ session('error') }}</div>
        @endif

        @if ($user->role === 'admin')
            <div class="grid lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">Assign New Task</h2>
                    <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Student</label>
                                <select name="user_id" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Task Title</label>
                                <input name="title" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Example: Submit attendance report">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Description</label>
                                <textarea id="task-description" name="description" rows="6" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter task instructions..."></textarea>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Attachment</label>
                                <input name="attachment" type="file" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Due Date</label>
                                <input name="due_date" type="date" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full mt-4 rounded-2xl bg-indigo-600 text-white px-4 py-3 font-semibold hover:bg-indigo-700 transition">Assign Task</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl shadow p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-4">Task Summary</h2>
                    <div class="grid gap-4">
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <p class="text-sm text-slate-500">Total Tasks</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $tasks->count() }}</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <p class="text-sm text-slate-500">Submitted</p>
                            <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $tasks->where('status', 'submitted')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">{{ $user->role === 'admin' ? 'All Tasks' : 'Your Assigned Tasks' }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">{{ $task->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                    @if ($user->role === 'admin')
                                        {{ $task->user->name }}
                                    @else
                                        {{ $task->assignedBy->name }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700">{{ optional($task->due_date)->format('Y-m-d') ?? 'No deadline' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-{{ $task->status === 'approved' ? 'emerald' : ($task->status === 'rejected' ? 'rose' : ($task->status === 'submitted' ? 'amber' : 'sky')) }}-100 px-3 py-1 text-sm font-semibold text-{{ $task->status === 'approved' ? 'emerald' : ($task->status === 'rejected' ? 'rose' : ($task->status === 'submitted' ? 'amber' : 'sky')) }}-700">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if ($user->role === 'student')
                                        @if ($task->status === 'pending' || $task->status === 'rejected')
                                            <button onclick="document.getElementById('respond-{{ $task->id }}').classList.toggle('hidden')"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Submit Response</button>
                                        @else
                                            <span class="text-slate-500">No action needed</span>
                                        @endif
                                    @else
                                        @if ($task->status === 'submitted')
                                            <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-2">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <select name="status" class="w-full rounded-2xl border border-slate-300 px-3 py-2">
                                                        <option value="approved">Approve</option>
                                                        <option value="rejected">Reject</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <textarea name="feedback" rows="2" class="w-full rounded-2xl border border-slate-300 px-3 py-2" placeholder="Feedback (optional)">{{ $task->feedback }}</textarea>
                                                </div>
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">Save</button>
                                            </form>
                                        @else
                                            <span class="text-slate-500">Waiting</span>
                                        @endif
                                    @endif

                                    @if ($task->task_file_path)
                                        <p class="mt-2 text-sm text-slate-600">
                                            Task file: <a href="{{ route('tasks.download', [$task, 'attachment']) }}" class="text-indigo-600 hover:underline">Download</a>
                                        </p>
                                    @endif
                                    @if ($task->response_file_path)
                                        <p class="mt-2 text-sm text-slate-600">
                                            Response file: <a href="{{ route('tasks.download', [$task, 'response']) }}" class="text-indigo-600 hover:underline">Download</a>
                                        </p>
                                    @endif
                                </td>
                            </tr>
                            @if ($user->role === 'student')
                                <tr id="respond-{{ $task->id }}" class="hidden bg-slate-50">
                                    <td colspan="5" class="px-6 py-4">
                                        <form method="POST" action="{{ route('tasks.respond', $task) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="grid gap-4">
                                                <textarea name="response" rows="3" class="w-full rounded-2xl border border-slate-300 px-3 py-2" placeholder="Explain how you completed the task..."></textarea>
                                                <div>
                                                    <label class="text-sm font-medium text-slate-700">Upload File</label>
                                                    <input type="file" name="response_file" class="mt-2 w-full rounded-2xl border border-slate-300 px-3 py-2" />
                                                </div>
                                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Send Response</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">No tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editorElement = document.querySelector('#task-description');
            if (editorElement) {
                ClassicEditor.create(editorElement).catch(error => console.error(error));
            }
        });
    </script>
@endsection