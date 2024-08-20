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
        'user_id',
        'in_status',
        'out_status'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
}
