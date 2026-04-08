<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin();
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $user->isAdmin();
    }
}
