<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarScheduleTarget extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'calendar_schedule_id',
        'target_type',
        'target_id'];

    public function calendarSchedule()
    {
        return $this->belongsTo(CalendarSchedule::class);
    }
}
