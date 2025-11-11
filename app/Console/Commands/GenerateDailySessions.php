<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class GenerateDailySessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sessions:generate';

    /**
     * The console command description.
     */
    protected $description = 'Automatically generate class sessions and attendance records for today\'s schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $dayOfWeek = $today->format('l'); // e.g., "Monday"

        $this->info("Generating sessions for {$dayOfWeek} ({$today->toDateString()})...");

        $schedules = ClassSchedule::with([
            'classCourse.enrollments.student'
        ])->where('day_of_week', $dayOfWeek)->get();

        $sessionCount = 0;
        $attendanceCount = 0;

        foreach ($schedules as $schedule) {
            $exists = ClassSession::where('class_schedule_id', $schedule->id)
                ->whereDate('session_date', $today->toDateString())
                ->exists();

            if ($exists) continue;

            $session = ClassSession::create([
                'class_schedule_id' => $schedule->id,
                'session_date' => $today->toDateString(),
                'cancelled' => false,
                'integrity_flag' => true,
            ]);

            $sessionCount++;

            foreach ($schedule->classCourse->enrollments as $enrollment) {
                AttendanceRecord::create([
                    'student_id' => $enrollment->student_id,
                    'class_session_id' => $session->id,
                    'status' => 'status', // default placeholder
                    'integrity_flag' => true,
                ]);
                $attendanceCount++;
            }
        }

        $this->info("✅ Created {$sessionCount} sessions and {$attendanceCount} attendance records.");

        return Command::SUCCESS;
    }
}
