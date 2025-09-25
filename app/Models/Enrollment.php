<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class_course_id',
        'student_id'];

    public function classCourse()
    {
        return $this->belongsTo(ClassCourse::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
