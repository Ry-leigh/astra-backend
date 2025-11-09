<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ProgramSeeder::class,
            ClassroomSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class,
            InstructorSeeder::class,
            StudentSeeder::class,
            ClassCourseSeeder::class,
            EnrollmentSeeder::class,
            AnnouncementSeeder::class,
            AnnouncementTargetSeeder::class,
            TaskSeeder::class,
            SubmissionSeeder::class,
            GradeSeeder::class,
            CalendarScheduleSeeder::class,
            CalendarScheduleTargetSeeder::class,
            ClassScheduleSeeder::class,
            ClassSessionSeeder::class,
            AttendanceRecordSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
