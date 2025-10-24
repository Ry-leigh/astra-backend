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
            ['program_id' => 1, 'year_level' => 1, 'section' => ''],
            ['program_id' => 5, 'year_level' => 3, 'section' => 'B'],
        ]);
    }
}
