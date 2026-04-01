<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $leaveRequests = LeaveRequest::with('user')->latest()->get();

            return view('leave.index', compact('user', 'leaveRequests'));
        }

        $leaveRequests = $user->leaveRequests()->latest()->get();

        return view('leave.index', compact('user', 'leaveRequests'));
    }

    public function store(Request $request, WhatsAppNotifier $notifier)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        $leaveRequest = $user->leaveRequests()->create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $notifier->sendUser($admin, "{$user->name} submitted a leave request from {$leaveRequest->start_date->format('Y-m-d')}.");
        }

        $notifier->sendUser($user, 'Your leave request has been submitted and is pending admin approval.');

        return back()->with('success', 'Leave request submitted.');
    }

    public function update(Request $request, LeaveRequest $leaveRequest, WhatsAppNotifier $notifier)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'admin_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $leaveRequest->update([
            'status' => $request->status,
            'admin_comment' => $request->admin_comment,
        ]);

        $notifier->sendUser($leaveRequest->user, "Your leave request has been {$leaveRequest->status}. Comment: {$request->admin_comment}");

        return back()->with('success', 'Leave request status updated.');
    }
}
