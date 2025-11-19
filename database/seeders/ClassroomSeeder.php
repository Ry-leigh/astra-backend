<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        Classroom::insert([
            ['program_id' => 1, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            ['program_id' => 1, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 2, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 2, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 2, 'year_level' => 3, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 2, 'year_level' => 4, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 3, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 3, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 3, 'year_level' => 3, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 3, 'year_level' => 4, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 4, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 4, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 4, 'year_level' => 3, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 4, 'year_level' => 4, 'section' => '', 'academic_year_id' => 1],
            ['program_id' => 2, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            ['program_id' => 2, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            ['program_id' => 2, 'year_level' => 3, 'section' => 'A', 'academic_year_id' => 1],
            ['program_id' => 2, 'year_level' => 3, 'section' => 'B', 'academic_year_id' => 1],
            ['program_id' => 2, 'year_level' => 4, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 6, 'year_level' => 1, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 6, 'year_level' => 2, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 6, 'year_level' => 3, 'section' => '', 'academic_year_id' => 1],
            // ['program_id' => 6, 'year_level' => 4, 'section' => '', 'academic_year_id' => 1],
        ]);
    }
}
