<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::insert([
            [
                'class_course_id' => 6,
                'title' => 'Quiz 1 - Finals',
                'description' => '...',
                'due_date' => '2025-11-07',
                'category' => 'quiz'
            ],
        ]);
    }
}
