<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'System',
            'last_name'  => 'Admin',
            'city'       => 'Marikina',
            'town'       => 'Marikina Heights',
            'province'   => 'NCR',
            'email'      => 'admin@onetap.test',
            'password'   => Hash::make('admin123'),
        ]);
        
        $admin->roles()->attach(Role::where('name', 'Administrator')->first());

        $faculty = User::create([
            'first_name' => 'Faculty',
            'last_name'  => 'Dummy',
            'sex'        => 'M',
            'city'       => 'Metro Manila',
            'town'       => 'Pasig',
            'province'   => 'NCR',
            'email'      => 'faculty@onetap.test',
            'password'   => Hash::make('faculty123'),
        ]);

        $faculty->roles()->attach(Role::where('name', 'Faculty')->first());

        $instructor = User::create([
            'first_name' => 'Instructor',
            'last_name'  => 'Dummy',
            'sex'        => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Pasay',
            'province'   => 'NCR',
            'email'      => 'instructor@onetap.test',
            'password'   => Hash::make('instructor123'),
        ]);

        $instructor->roles()->attach(Role::where('name', 'Instructor')->first());

        $officer = User::create([
            'first_name' => 'Class Secretary',
            'last_name'  => 'Dummy',
            'sex'        => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Makati',
            'province'   => 'NCR',
            'email'      => 'secretary@onetap.student.test',
            'password'   => Hash::make('secretary123'),
        ]);

        $officer->roles()->attach(Role::where('name', 'Officer')->first());

        $student = User::create([
            'first_name' => 'Student',
            'last_name'  => 'Dummy',
            'sex'        => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Makati',
            'province'   => 'NCR',
            'email'      => 'student@onetap.student.test',
            'password'   => Hash::make('student123'),
        ]);

        $student->roles()->attach(Role::where('name', 'Student')->first());
    }
}
