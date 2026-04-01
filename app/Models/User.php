<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Task;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
        'whatsapp_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public static function attendanceGrade(int $presentCount): string
    {
        if ($presentCount >= 26) {
            return 'A';
        }

        if ($presentCount >= 20) {
            return 'B';
        }

        if ($presentCount >= 15) {
            return 'C';
        }

        if ($presentCount >= 10) {
            return 'D';
        }

        return 'F';
    }

    public function roleLabel(): string
    {
        return config('roles.labels.' . $this->role, ucfirst($this->role));
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = config('roles.permissions.' . $this->role, []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }
}
