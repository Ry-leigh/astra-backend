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
            [
                'name' => 'Business Process Management',
                'description' => 'N/A',
                'code' => 'BPM',
                'units' => 3
            ],
            [
                'name' => 'Financial Management',
                'description' => 'N/A',
                'code' => 'FinMan',
                'units' => 3
            ],
            [
                'name' => 'Statistical Analysis in Information System',
                'description' => 'N/A',
                'code' => 'StatsAna',
                'units' => 3
            ],
            [
                'name' => 'Gender and Society',
                'description' => 'N/A',
                'code' => 'GenSoc',
                'units' => 3
            ],
            [
                'name' => 'Life and Works of Rizal',
                'description' => 'N/A',
                'code' => 'LWR',
                'units' => 3
            ],
            [
                'name' => 'IS Project Management',
                'description' => 'N/A',
                'code' => 'ISPM',
                'units' => 3
            ],
            [
                'name' => 'Christian Teachings 5',
                'description' => 'N/A',
                'code' => 'CT5',
                'units' => 2
            ],
            [
                'name' => 'Application Development',
                'description' => 'N/A',
                'code' => 'AppDev',
                'units' => 5
            ],
        ]);
    }
}
