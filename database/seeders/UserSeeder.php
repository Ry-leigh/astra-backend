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
            'first_name' => 'Mira',
            'last_name'  => 'Langford',
            'sex'        => 'F',
            'address'    => '',
            'email'      => 'admin@onetap.test',
            'password'   => Hash::make('admin123'),
        ]);
        
        $admin->roles()->attach(Role::where('name', 'Administrator')->first());

        $instructor = User::create([
            'first_name' => 'Hana',
            'last_name'  => 'Park',
            'sex'        => 'F',
            'address'    => '',
            'email'      => 'hanapark@onetap.test',
            'password'   => Hash::make('instructor123'),
        ]);

        $instructor->roles()->attach(Role::where('name', 'Instructor')->first());

        $instructor = User::create([
            'first_name' => 'Julian',
            'last_name'  => 'Cross',
            'sex'        => 'M',
            'address'    => '',
            'email'      => 'juliancross@onetap.test',
            'password'   => Hash::make('instructor123'),
        ]);

        $instructor->roles()->attach(Role::where('name', 'Instructor')->first());

        $officer = User::create([
            'first_name' => 'Liora',
            'last_name'  => 'Valdez',
            'sex'        => 'F',
            'address'    => '',
            'email'      => 'lioravaldez@student.onetap.test',
            'password'   => Hash::make('student123'),
        ]);

        $officer->roles()->attach(Role::where('name', 'Officer')->first());

        $student = User::create([
            'first_name' => 'Keon',
            'last_name'  => 'Sullivan',
            'sex'        => 'M',
            'address'    => '',
            'email'      => 'student@student.onetap.test',
            'password'   => Hash::make('student123'),
        ]);

        $student->roles()->attach(Role::where('name', 'Student')->first());
    }
}
