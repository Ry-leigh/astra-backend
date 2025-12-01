<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $fillable = [
        'class_schedule_id',
        'calendar_schedule_id',
        'substitute_id',
        'session_date',
        'time_in',
        'time_out',
        'remarks',
        'marked_by',
        'integrity_flag'
    ];

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function calendarSchedule()
    {
        return $this->belongsTo(CalendarSchedule::class);
    }

    public function substitute()
    {
        return $this->belongsTo(User::class, 'substitute_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
