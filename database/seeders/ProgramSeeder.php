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
            ['id' => 1, 'name' => 'Associate in Computer Technology', 'description' => '2 year IT-related program'],
            ['id' => 2, 'name' => 'Bachelor of Arts in Broadcasting', 'description' => 'Broadcasting'],
            ['id' => 3, 'name' => 'Bachelor of Science in Accountancy', 'description' => 'Accounting'],
            ['id' => 4, 'name' => 'Bachelor of Science in Accounting Information Systems', 'description' => 'Accounting Information Systems'],
            ['id' => 5, 'name' => 'Bachelor of Science in Information Systems', 'description' => 'IT-focused program'],
            ['id' => 6, 'name' => 'Bachelor of Science in Social Work', 'description' => 'Social Work']
        ]);
    }
}
