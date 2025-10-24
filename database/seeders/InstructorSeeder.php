<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        Instructor::insert([
            ['user_id' => 2],
            ['user_id' => 3],
        ]);
    }
}
