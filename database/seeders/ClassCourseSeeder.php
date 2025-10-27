<?php

namespace Database\Seeders;

use App\Models\ClassCourse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassCourseSeeder extends Seeder
{
    public function run(): void
    {
        ClassCourse::factory()->count(100)->create();
    }
}
