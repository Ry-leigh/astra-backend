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
            [
                'user_id' => 2,
                'program_id' => 2
            ],
            [
                'user_id' => 3,
                'program_id' => null
            ],
            [
                'user_id' => 4,
                'program_id' => null
            ],
            [
                'user_id' => 5,
                'program_id' => null
            ],
            [
                'user_id' => 6,
                'program_id' => null
            ],
            [
                'user_id' => 7,
                'program_id' => 2
            ],
            [
                'user_id' => 8,
                'program_id' => null
            ],
            [
                'user_id' => 9,
                'program_id' => 2
            ]
        ]);
    }
}
