<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AnnouncementTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Announcement::insert([
            [
                'created_by' => 1,
                'title' => 'Mental Health Seminar',
                'description' => 'Seminar about and for student`s mental health',
                'event_date' => '2025-11-06',
                'event_time' => '13:00:00',
                'last_updated_by' => null
            ],
        ]);
    }
}
