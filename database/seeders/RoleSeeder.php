<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['name' => 'Administrator', 'description' => 'Full system access'],
            ['name' => 'Instructor', 'description' => 'Teaches classes and manages tasks'],
            ['name' => 'Officer', 'description' => 'Student with authority'],
            ['name' => 'Student', 'description' => 'Enrolled in courses'],
        ]);
    }
}
