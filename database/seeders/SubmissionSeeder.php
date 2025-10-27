<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        Student::insert([
            ["user_id" => 3, "program_id" => 5, "year_level" => 3],
            ["user_id" => 4, "program_id" => 5, "year_level" => 3]
        ]);
    }
}
