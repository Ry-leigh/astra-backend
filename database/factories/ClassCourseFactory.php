<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassCourse>
 */
class ClassCourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id'  => Classroom::inRandomOrder()->value('id'),
            'course_id'     => Course::inRandomOrder()->value('id'),
            'instructor_id' => Instructor::inRandomOrder()->value('id'),
            'semester'      => 1,
        ];
    }
}
