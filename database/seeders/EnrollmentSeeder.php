<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        Enrollment::insert([
            ['class_course_id' => 1, 'student_id' => 2],
            ['class_course_id' => 2, 'student_id' => 2],
        ]);
    }
}
