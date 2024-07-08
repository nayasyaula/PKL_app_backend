<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;

class ToDoList extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'status',
        'date',    
        'user_id',
        'attendance_id',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function attendance()
    // {
    //     return $this->belongsTo(Attendance::class);
    // }
}
