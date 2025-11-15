<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        Program::insert([
            [
                'name' => 'Associate in Computer Technology', 
                'description' => '2 year IT-related program',
                'color' => 'FFC068'
            ],
            // [
            //     'name' => 'Bachelor of Arts in Broadcasting', 
            //     'description' => 'Trains students for careers in media such as creating producing, and managing content for various platforms',
            //     'color' => 'A1ECFF'
            // ],
            // [ 
            //     'name' => 'Bachelor of Science in Accountancy', 
            //     'description' => 'Accounting',
            //     'color' => 'FFEE8C'
            // ],
            // [
            //     'name' => 'Bachelor of Science in Accounting Information Systems', 
            //     'description' => 'Accounting Information Systems',
            //     'color' => 'FFEE8C'
            // ],
            [
                'name' => 'Bachelor of Science in Information Systems', 
                'description' => 'IT-focused program',
                'color' => 'FF7B6F'
            ],
            // [
            //     'name' => 'Bachelor of Science in Social Work', 
            //     'description' => 'Social Work',
            //     'color' => 'D9BBFF'
            // ]
        ]);
    }
}
