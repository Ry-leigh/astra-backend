<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        Semester::insert([
            [
                'academic_year_id' => 1,
                'semester' => 1,
                'start_date' => '2025-07-04',
                'end_date' => '2025-12-16',
            ],
            [
                'academic_year_id' => 1,
                'semester' => 2,
                'start_date' => '2026-01-05',
                'end_date' => null,
            ],
        ]);
    }
}
