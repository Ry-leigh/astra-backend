<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'task_id',
        'student_id',
        'is_finished',
        'finished_at'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
