<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'all_day',
        'start_time',
        'end_time',
        'category',
        'class_course_id',
        'room',
        'repeats',
        'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets()
    {
        return $this->hasMany(CalendarScheduleTarget::class);
    }

    public function classCourse()
    {
        return $this->belongsTo(ClassCourse::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}
