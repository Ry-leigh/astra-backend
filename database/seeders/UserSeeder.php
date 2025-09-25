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
            'first_name' => 'Mark',
            'last_name'  => 'Fernandez',
            'sex' => 'M',
            'city'       => 'Metro Manila',
            'town'       => 'Pasig',
            'province'   => 'NCR',
            'email'      => 'mark@onetap.test',
            'password'   => Hash::make('password123'),
        ]);

        $faculty->roles()->attach(Role::where('name', 'Faculty')->first());

        $instructor = User::create([
            'first_name' => 'Yvonne',
            'last_name'  => 'Lopez',
            'sex' => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Pasay',
            'province'   => 'NCR',
            'email'      => 'yvonne@onetap.test',
            'password'   => Hash::make('password123'),
        ]);

        $instructor->roles()->attach(Role::where('name', 'Instructor')->first());

        $officer = User::create([
            'first_name' => 'Shandi',
            'last_name'  => 'Dope',
            'sex' => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Makati',
            'province'   => 'NCR',
            'email'      => 'shandidope@onetap.student.test',
            'password'   => Hash::make('password123'),
        ]);

        $officer->roles()->attach(Role::where('name', 'Officer')->first());

        $student = User::create([
            'first_name' => 'Eu',
            'last_name'  => 'Nice',
            'sex' => 'F',
            'city'       => 'Metro Manila',
            'town'       => 'Makati',
            'province'   => 'NCR',
            'email'      => 'eunice@onetap.student.test',
            'password'   => Hash::make('password123'),
        ]);

        $student->roles()->attach(Role::where('name', 'Student')->first());
    }
}
