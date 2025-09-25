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
        'schedule_type',
        'related_id',
        'start_datetime',
        'end_datetime',
        'is_all_day',
        'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets()
    {
        return $this->hasMany(CalendarScheduleTarget::class);
    }
}
