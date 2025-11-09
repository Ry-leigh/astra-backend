<?php

namespace Database\Seeders;

use App\Models\CalendarScheduleTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalendarScheduleTargetSeeder extends Seeder
{
    public function run(): void
    {
        CalendarScheduleTarget::insert([
            [
                'calendar_schedule_id' => 1,
                'target_type' => 'global'
            ],
        ]);
    }
}
