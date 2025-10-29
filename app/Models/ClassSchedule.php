<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class_course_id',
        'start_datetime',
        'end_datetime',
        'status',
        'topic'];

    public function classCourse()
    {
        return $this->belongsTo(ClassCourse::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function calendarSchedules()
    {
        return $this->hasMany(CalendarSchedule::class);
    }
}
