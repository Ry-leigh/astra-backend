<?php

namespace Database\Factories;

use App\Models\ClassCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
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
            'title'             => $this->faker->words(3, true),
            'description'       => $this->faker->sentence(),
            'due_date'          => $this->faker->date(),
            'due_time'          => $this->faker->time(),
            'category'          => $this->faker->randomElement(['assignment', 'project', 'quiz', 'exam', 'activity', 'other']),
        ];
    }
}
