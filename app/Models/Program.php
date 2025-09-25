<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description'];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function instructors()
    {
        return $this->hasMany(Instructor::class);
    }
}
