<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::insert([
            ['name' => 'Application Development 1', 'description' => 'React JS', 'code' => 'AppDev1'],
            ['name' => 'IS Project Management 1', 'description' => 'Project Management', 'code' => 'ISPM1'],
        ]);
    }
}
