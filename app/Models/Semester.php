<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'semester_number',
        'start_date',
        'end_date'
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
