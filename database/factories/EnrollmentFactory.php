<?php

namespace Database\Factories;

use App\Models\ClassCourse;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_course_id'   => ClassCourse::inRandomOrder()->value('id'),
            'student_id'        => Student::inRandomOrder()->value('id'),
        ];
    }
}
