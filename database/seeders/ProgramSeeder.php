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
            ['name' => 'Associate in Computer Technology', 'description' => '2 year IT-related program'],
            ['name' => 'Bachelor of Arts in Broadcasting', 'description' => 'Broadcasting'],
            ['name' => 'Bachelor of Science in Accountancy', 'description' => 'Accounting'],
            ['name' => 'Bachelor of Science in Accounting Information Systems', 'description' => 'Accounting Information Systems'],
            ['name' => 'Bachelor of Science in Information Systems', 'description' => 'IT-focused program'],
            ['name' => 'Bachelor of Science in Social Work', 'description' => 'Social Work']
        ]);
    }
}
