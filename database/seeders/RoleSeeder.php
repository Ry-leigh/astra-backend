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
            ['id' => 1, 'name' => 'Administrator', 'description' => 'Full system access'],
            ['id' => 2, 'name' => 'Instructor', 'description' => 'Teaches classes and manages tasks'],
            ['id' => 3, 'name' => 'Officer', 'description' => 'Student class officer'],
            ['id' => 4, 'name' => 'Student', 'description' => 'Enrolled in courses'],
        ]);
    }
}
