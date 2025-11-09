<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassScheduleSeeder extends Seeder
{
    public function run(): void
    {
        ClassSchedule::insert([
            [
                'class_course_id' => 1,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00'
            ],
            [
                'class_course_id' => 2,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Monday',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00'
            ],
            [
                'class_course_id' => 3,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Monday',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00'
            ],
            [
                'class_course_id' => 4,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Monday',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00'
            ],
            [
                'class_course_id' => 5,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Monday',
                'start_time' => '16:00:00',
                'end_time' => '17:00:00'
            ],
            [
                'class_course_id' => 5,
                'room' => 'EFS 402',
                'day_of_week' => 'Tuesday',
                'start_time' => '07:00:00',
                'end_time' => '09:00:00'
            ],
            [
                'class_course_id' => 2,
                'room' => 'EFS 402',
                'day_of_week' => 'Tuesday',
                'start_time' => '09:00:00',
                'end_time' => '11:00:00'
            ],
            [
                'class_course_id' => 1,
                'room' => 'COMLAB A',
                'day_of_week' => 'Tuesday',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00'
            ],
            [
                'class_course_id' => 6,
                'room' => 'EFS 402',
                'day_of_week' => 'Tuesday',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00'
            ],
            [
                'class_course_id' => 4,
                'room' => 'EFS 402',
                'day_of_week' => 'Wednesday',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00'
            ],
            [
                'class_course_id' => 7,
                'room' => 'EFS 402',
                'day_of_week' => 'Wednesday',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00'
            ],
            [
                'class_course_id' => 8,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Wednesday',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00'
            ],
            [
                'class_course_id' => 3,
                'room' => 'EFS 402',
                'day_of_week' => 'Thursday',
                'start_time' => '09:00:00',
                'end_time' => '11:00:00'
            ],
            [
                'class_course_id' => 6,
                'room' => 'Asynch/Synch',
                'day_of_week' => 'Friday',
                'start_time' => '17:00:00',
                'end_time' => '18:00:00'
            ],
            [
                'class_course_id' => 8,
                'room' => 'COMLAB A',
                'day_of_week' => 'Saturday',
                'start_time' => '08:00:00',
                'end_time' => '11:00:00'
            ],
        ]);
    }
}
