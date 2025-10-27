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
        Announcement::factory()->count(50)->create();
        AnnouncementTarget::factory()->count(50)->create();
    }
}
