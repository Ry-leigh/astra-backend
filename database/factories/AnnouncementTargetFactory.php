<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Program;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnnouncementTarget>
 */
class AnnouncementTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetType = $this->faker->randomElement(['global', 'role', 'program', 'classroom', 'course']);

        $targetId = match ($targetType) {
            'role'      => Role::inRandomOrder()->value('id'),
            'program'   => Program::inRandomOrder()->value('id'),
            'classroom' => Classroom::inRandomOrder()->value('id'),
            'course'    => Course::inRandomOrder()->value('id'),
            default     => null,
        };

        return [
            'announcement_id' => Announcement::inRandomOrder()->value('id'),
            'target_type'     => $targetType,
            'target_id'       => $targetId,
        ];
    }
}
