<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by'    => $this->faker->randomElement([1, 5, 6]),
            'title'         => $this->faker->words(3, true),
            'description'   => $this->faker->sentence(),
            'event_date'    => $this->faker->date(),
            'event_time'    => $this->faker->time(),
        ];
    }
}
