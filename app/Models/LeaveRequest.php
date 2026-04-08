<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'admin_comment',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function canBeViewedBy(User $user): bool
    {
        return $user->isAdmin() || $user->isHr() || $user->id === $this->user_id;
    }

    public function canBeApprovedBy(User $user): bool
    {
        return $user->hasPermission('approve-leave');
    }
}
