<?php

namespace Database\Seeders;

use App\Models\AnnouncementTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementTargetSeeder extends Seeder
{
    public function run(): void
    {
        AnnouncementTarget::insert([
            [
                'announcement_id' => 1,
                'target_type' => 'global'
            ],
        ]);
    }
}
