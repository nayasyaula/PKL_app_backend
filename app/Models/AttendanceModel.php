<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'in',
        'out',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function todolist()
    {
        return $this->hasMany(ToDoList::class, 'attendance_id', 'id');
    }


    
}
