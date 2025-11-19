<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'year_start',
        'year_end'
    ];

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
    
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
