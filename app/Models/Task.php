<?php

namespace App\Models;

use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_by',
        'title',
        'description',
        'task_file_path',
        'due_date',
        'status',
        'response',
        'response_file_path',
        'feedback',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)->latestOfMany();
    }

    public function canBeViewedBy(User $user): bool
    {
        return $user->isAdmin() || $user->id === $this->user_id || $user->id === $this->assigned_by;
    }

    public function canBeRespondedBy(User $user): bool
    {
        return $user->id === $this->user_id && $user->hasPermission('submit-task');
    }

    public function canBeApprovedBy(User $user): bool
    {
        return $user->hasPermission('approve-task');
    }

    public function canBeAssignedBy(User $user): bool
    {
        return $user->hasPermission('assign-task');
    }
}
