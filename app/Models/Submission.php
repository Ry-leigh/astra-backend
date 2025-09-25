<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'task_id',
        'student_id',
        'file_url',
        'text_submission',
        'submitted_at'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
