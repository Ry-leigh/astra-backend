<?php

namespace Database\Seeders;

use App\Models\CalendarSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalendarScheduleSeeder extends Seeder
{
    public function run(): void
    {
        CalendarSchedule::insert([
            [
                'title' => 'Mental Health Seminar',
                'description' => 'Seminar about and for student`s mental health',
                'start_date' => '2025-11-06',
                'start_time' => '13:00:00',
                'category' => 'event',
                'created_by' => 1
            ],
        ]);
    }
}
