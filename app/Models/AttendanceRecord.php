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
        'instructor_time_in',
        'instructor_time_out',
        'status',
        'student_time_in',
        'remarks',
        'marked_by'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
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
