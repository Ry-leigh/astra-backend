<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class_course_id',
        'title',
        'description',
        'due_date', 
        'due_time',
        'category'];

    public function classCourse()
    {
        return $this->belongsTo(ClassCourse::class);
    }

    public function statuses()
    {
        return $this->hasMany(TaskStatus::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
