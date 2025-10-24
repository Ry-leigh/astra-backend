<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::insert([
            ['user_id' => 4, 'program_id' => 1, 'year_level' => '1', 'section' => null],
            ['user_id' => 5, 'program_id' => 5, 'year_level' => '3', 'section' => 'B'],   
        ]);
    }
}
