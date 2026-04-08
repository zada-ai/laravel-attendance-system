<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'response',
        'response_file_path',
        'status',
        'feedback',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
