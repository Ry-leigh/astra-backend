<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'class_session_id',
        'status',
        'time_in',
        'remarks',
        'marked_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function substitute()
    {
        return $this->belongsTo(User::class, 'substitute_id');
    }
}
