<?php

namespace Database\Seeders;

use App\Models\ClassCourse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassCourseSeeder extends Seeder
{
    public function run(): void
    {
        ClassCourse::insert([
            ['classroom_id' => 1, 'course_id' => 1, 'instructor_id' => 2, 'semester' => 1, 'academic_year' => '2025 - 2026'],
            ['classroom_id' => 2, 'course_id' => 2, 'instructor_id' => 1, 'semester' => 1, 'academic_year' => '2025 - 2026'],
        ]);
    }
}
