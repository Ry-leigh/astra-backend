<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'program_id',
        'year_level',
        'section',
        'academic_year_id'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function classCourses()
    {
        return $this->hasMany(ClassCourse::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
