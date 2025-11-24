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
            [
                'classroom_id' => 6,
                'course_id' => 1,
                'instructor_id' => 1,
                'semester_id' => 1,
                'color' => 'FFC068'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 2,
                'instructor_id' => 3,
                'semester_id' => 1,
                'color' => 'FFEE8C'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 3,
                'instructor_id' => 2,
                'semester_id' => 1,
                'color' => '8BEFA7'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 4,
                'instructor_id' => 4,
                'semester_id' => 1,
                'color' => 'D9BBFF'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 5,
                'instructor_id' => 5,
                'semester_id' => 1,
                'color' => 'EC6A5E'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 6,
                'instructor_id' => 6,
                'semester_id' => 1,
                'color' => 'FFB8B8'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 7,
                'instructor_id' => 7,
                'semester_id' => 1,
                'color' => 'D5D8E4'
            ],
            [
                'classroom_id' => 6,
                'course_id' => 8,
                'instructor_id' => 8,
                'semester_id' => 1,
                'color' => 'A1ECFF'
            ]
        ]);
    }
}
