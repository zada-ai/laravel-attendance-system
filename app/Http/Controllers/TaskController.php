<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::with(['user', 'assignedBy'])->latest()->get();
            $students = User::where('role', 'student')->get();

            return view('tasks.index', compact('user', 'tasks', 'students'));
        }

        $tasks = $user->tasks()->latest()->get();

        return view('tasks.index', compact('user', 'tasks'));
    }

    public function store(Request $request, WhatsAppNotifier $notifier)
    {
        if (! Auth::user()->hasPermission('assign-task')) {
            abort(403);
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ]);

        $taskData = [
            'user_id' => $request->user_id,
            'assigned_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ];

        if ($request->hasFile('attachment')) {
            $taskData['task_file_path'] = $request->file('attachment')->store('task_uploads', 'public');
        }

        $task = Task::create($taskData);

        $student = $task->user;
        $notifier->sendUser($student, "A new task has been assigned to you: {$task->title}. Please check your dashboard.");

        return back()->with('success', 'Task assigned successfully.');
    }

    public function respond(Request $request, Task $task, WhatsAppNotifier $notifier)
    {
        $this->authorizePermission('submit-task');

        $user = Auth::user();

        if ($user->id !== $task->user_id) {
            abort(403);
        }

        if (! in_array($task->status, ['pending', 'rejected'])) {
            return back()->with('error', 'This task cannot be updated now.');
        }

        $request->validate([
            'response' => ['required', 'string', 'max:2000'],
            'response_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $submissionData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'response' => $request->response,
            'status' => 'pending',
        ];

        if ($request->hasFile('response_file')) {
            $submissionData['response_file_path'] = $request->file('response_file')->store('task_responses', 'public');
        }

        TaskSubmission::create($submissionData);
        $task->update(['status' => 'submitted']);

        $notifier->sendUser(
            $task->assignedBy,
            "{$user->name} has submitted a task response for '{$task->title}'. Please review the submission."
        );

        return back()->with('success', 'Task response submitted.');
    }

    public function update(Request $request, Task $task, WhatsAppNotifier $notifier)
    {
        if (! Auth::user()->hasPermission('approve-task')) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission = $task->submissions()->latest()->first();

        if ($submission) {
            $submission->update([
                'status' => $request->status,
                'feedback' => $request->feedback,
            ]);
        }

        $task->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
        ]);

        $notifier->sendUser($task->user, "Your task '{$task->title}' has been {$task->status}. Feedback: {$request->feedback}");

        return back()->with('success', 'Task status updated.');
    }

    public function download(Task $task, string $type)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->id !== $task->user_id && $user->id !== $task->assigned_by) {
            abort(403);
        }

        if ($type === 'attachment' && $task->task_file_path) {
            return Storage::disk('public')->download($task->task_file_path);
        }

        if ($type === 'response') {
            $submission = $task->submissions()->latest()->first();

            if ($submission && $submission->response_file_path) {
                return Storage::disk('public')->download($submission->response_file_path);
            }

            if ($task->response_file_path) {
                return Storage::disk('public')->download($task->response_file_path);
            }
        }

        abort(404);
    }
}
