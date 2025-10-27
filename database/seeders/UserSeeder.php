<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;

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

        $users = User::factory()->count(200)->create();

        $adminUsers         = $users->skip(4)->take(2);
        $instructorUsers    = $users->skip(6)->take(20);
        $officerUsers       = $users->skip(26)->take(20);
        $studentUsers       = $users->skip(46);

        $adminUsers->each(fn($user) => $user->roles()->attach(1));
        $instructorUsers->each(fn($user) => $user->roles()->attach(2));
        $officerUsers->each(fn($user) => $user->roles()->attach(3));
        $studentUsers->each(fn($user) => $user->roles()->attach(4));

        $instructorUsers->each(function ($user) {
            Instructor::create([
                'user_id'       => $user->id,
                'program_id'    => collect([null, 1, 5])->random(),
            ]);
        });

        $officerUsers->each(function ($user) {
            Student::create([
                'user_id' => $user->id,
                'program_id' => fake()->randomElement([1, 5]),
                'year_level' => fake()->numberBetween(1, 4),
            ]);
        });

        $studentUsers->each(function ($user) {
            Student::create([
                'user_id' => $user->id,
                'program_id' => fake()->randomElement([1, 5]),
                'year_level' => fake()->numberBetween(1, 4),
            ]);
        });
    }
}
