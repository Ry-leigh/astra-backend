<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        ClassSession::insert([
            [
                'class_schedule_id' => 7,
                'session_date' => '2025-11-04',
                'cancelled' => false,
                'time_in' => '09:02:00',
                'time_out' => '11:00:00',
                'marked_by' => 24,
            ],
            [
                'class_schedule_id' => 8,
                'session_date' => '2025-11-04',
                'cancelled' => true,
                'time_in' => null,
                'time_out' => null,
                'marked_by' => 24,
            ],
            [
                'class_schedule_id' => 9,
                'session_date' => '2025-11-04',
                'cancelled' => false,
                'time_in' => '17:04:00',
                'time_out' => '19:10:00',
                'marked_by' => 24,
            ],
        ]);
    }
}
