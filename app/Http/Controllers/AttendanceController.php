<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClassCourse;
use App\Models\ClassSession;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request, $classCourseId, $date = null)
    {
        $today = Carbon::today();
        $requested = $date ? Carbon::parse($date)->startOfDay() : $today->copy();

        $classCourse = ClassCourse::with(['classSchedules', 'calendarSchedules'])->findOrFail($classCourseId);

        if ($classCourse->classSchedules->isEmpty() && $classCourse->calendarSchedules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found.',
            ], 404);
        }

        if ($this->isClassDay($requested, $classCourse->classSchedules, $classCourse->calendarSchedules)) {
            $finalDate = $requested;
        } else {
            $recent = $this->findMostRecentScheduledDate($classCourse, $requested);
            $finalDate = $recent ?? $today->copy();        
        }

        // find or create session + attendance records and return response
        return $this->findOrCreateSessionWithAttendance($classCourse, $finalDate);
    }

    /**
     * Return the previous scheduled class day (and its session + records).
     * Uses the same creation logic as index().
     */
    public function previous(Request $request, $classCourseId, $date)
    {
        $classCourse = ClassCourse::with(['classSchedules', 'calendarSchedules'])->findOrFail($classCourseId);
        $current = Carbon::parse($date)->startOfDay();

        // Find previous scheduled date (searches both weekly schedules and calendar makeup)
        $prev = $this->getPreviousClassDate($current, $classCourse->classSchedules, $classCourse->calendarSchedules);

        if (! $prev) {
            return response()->json([
                'success' => false,
                'message' => 'No previous class session found.'
            ], 500);
        }

        return $this->index($request, $classCourseId, $prev->toDateString());
    }

    /**
     * Return the next scheduled class day (and its session + records).
     * Reject if next would be > today (can't go forward in time).
     */
    public function next(Request $request, $classCourseId, $date)
    {
        $classCourse = ClassCourse::with(['classSchedules', 'calendarSchedules'])->findOrFail($classCourseId);
        $current = Carbon::parse($date)->startOfDay();

        $next = $this->getNextClassDate($current, $classCourse->classSchedules, $classCourse->calendarSchedules);

        if (! $next) {
            return response()->json([
                'success' => false,
                'message' => "Can't go forward in time."
            ]);
        }

        // Defensive: ensure not > today
        if ($next->greaterThan(Carbon::today())) {
            return response()->json([
                'success' => false,
                'message' => "Can't go forward in time."
            ]);
        }

        return $this->index($request, $classCourseId, $next->toDateString());
    }

    /**
     * Create a single attendance record manually (still allowed even though sessions auto-created).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_session_id' => ['required', 'exists:class_sessions,id'],
            'student_id' => ['required', 'exists:students,id'],
            'status' => ['required', Rule::in(['present', 'late', 'absent', 'excused', 'suspended'])],
            'time_in' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $session = ClassSession::findOrFail($validated['class_session_id']);

        // Integrity flag: marking a session that's not today sets integrity flag
        $isIntegrityViolated = ! Carbon::parse($session->session_date)->isSameDay(Carbon::today());

        $record = AttendanceRecord::create([
            'class_session_id' => $validated['class_session_id'],
            'student_id' => $validated['student_id'],
            'status' => $validated['status'],
            'time_in' => $validated['time_in'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'marked_by' => Auth::id(),
            'integrity_flag' => $isIntegrityViolated,
        ]);

        return response()->json([
            'message' => 'Attendance record created successfully.',
            'record' => $record,
        ], 201);
    }

    /**
     * Update an attendance record (the most used method).
     */
    public function update(Request $request, $classCourseId, $attendanceId)
    {
        $record = AttendanceRecord::findOrFail($attendanceId);

        $validated = $request->validate([
            'status' => 'sometimes|in:status,present,late,absent,excused,suspended',
            'time_in' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255',
        ]);

        $validated['marked_by'] = Auth::id();

        $record->fill($validated);

        if (!$record->isDirty()) {
            return response()->json([
                'success' => false,
                'message' => 'Record received but no values changed.',
            ]);
        }

        // Now save cleanly
        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record updated successfully.',
            'record' => $record->refresh()
        ]);
    }

    protected function isClassDay(Carbon $date, $classSchedules, $calendarSchedules): bool
    {
        foreach ($calendarSchedules as $schedule) {
            if ($schedule->category === 'makeup_class') {
                $start = Carbon::parse($schedule->start_date)->startOfDay();
                $end = $schedule->end_date ? Carbon::parse($schedule->end_date)->startOfDay() : $start;
                if ($date->betweenIncluded($start, $end)) {
                    return true;
                }
            }
        }

        foreach ($classSchedules as $sched) {
            if (strtolower($date->format('l')) === strtolower($sched->day_of_week)) {
                return true;
            }
        }

        return false;
    }

    protected function findMostRecentScheduledDate(ClassCourse $classCourse, Carbon $referenceDate)
    {
        $possible = collect();

        // regular schedules: for each schedule, walk backwards to the most recent date with that day
        foreach ($classCourse->classSchedules as $sched) {
            // get most recent past date with that weekday, relative to referenceDate
            $recent = $this->mostRecentWeekdayOnOrBefore($referenceDate, $sched->day_of_week);
            if ($recent && $recent->lte(Carbon::today())) {
                $possible->push($recent);
            }
        }

        // calendar make-up schedules
        foreach ($classCourse->calendarSchedules as $cal) {
            if ($cal->category === 'makeup_class') {
                $start = Carbon::parse($cal->start_date)->startOfDay();
                // if multi-day makeup, consider all days between start and end
                $end = $cal->end_date ? Carbon::parse($cal->end_date)->startOfDay() : $start;

                // only include those <= referenceDate and <= today
                if ($start->lte($referenceDate)) {
                    // push start (we assume makeups are important points)
                    // If you want all days between start-end, you could push them individually
                    $possible->push($start);
                }
            }
        }

        if ($possible->isEmpty()) return null;

        // pick the latest (closest to referenceDate but not after)
        return $possible->filter(fn($d) => $d->lte($referenceDate))->sortByDesc(fn($d) => $d->timestamp)->first();
    }

    /**
     * Find previous class date before $currentDate (strictly earlier) - returns Carbon or null.
     */
    protected function getPreviousClassDate(Carbon $currentDate, $classSchedules, $calendarSchedules)
    {
        $date = $currentDate->copy()->subDay();
        $limit = 365; // safety limit (1 year back)
        for ($i = 0; $i < $limit; $i++) {
            if ($this->isClassDay($date, $classSchedules, $calendarSchedules)) {
                return $date;
            }
            $date->subDay();
        }
        return null;
    }

    /**
     * Find next class date after $currentDate (strictly later) but not after today.
     * Returns Carbon or null (null means none or would be > today).
     */
    protected function getNextClassDate(Carbon $currentDate, $classSchedules, $calendarSchedules)
    {
        $today = Carbon::today();
        $date = $currentDate->copy()->addDay();
        $limit = 365; // safety limit
        for ($i = 0; $i < $limit; $i++) {
            if ($date->greaterThan($today)) return null; // can't go forward in time
            if ($this->isClassDay($date, $classSchedules, $calendarSchedules)) {
                return $date;
            }
            $date->addDay();
        }
        return null;
    }

    protected function findOrCreateSessionWithAttendance(ClassCourse $classCourse, Carbon $date)
    {
        $matchingSchedule = $classCourse->classSchedules->first(fn($s) => strtolower($date->format('l')) === strtolower($s->day_of_week));

        $matchingCalendar = $classCourse->calendarSchedules->first(function($c) use ($date) {
            $start = Carbon::parse($c->start_date)->startOfDay();
            $end = $c->end_date ? Carbon::parse($c->end_date)->startOfDay() : $start;
            return $c->category === 'makeup_class' && $date->betweenIncluded($start, $end);
        });

        $sessionQuery = ClassSession::
        with([
            'classSchedule',
            'calendarSchedule'
        ])->whereDate('session_date', $date->toDateString());

        if ($matchingSchedule) {
            $sessionQuery->where('class_schedule_id', $matchingSchedule->id);
        } else {
            $sessionQuery->whereNull('class_schedule_id');
        }

        if ($matchingCalendar) {
            $sessionQuery->where('calendar_schedule_id', $matchingCalendar->id);
        } else {
            $sessionQuery->whereNull('calendar_schedule_id');
        }

        $session = $sessionQuery->first();

        if (! $session) {

            DB::beginTransaction();
            try {
                $session = ClassSession::firstOrCreate([
                    'class_schedule_id' => $matchingSchedule?->id,
                    'calendar_schedule_id' => $matchingCalendar?->id,
                    'session_date' => $date->toDateString(),
                ]);
                
                $enrollments = Enrollment::where('class_course_id', $classCourse->id)->get();

                foreach ($enrollments as $enrollment) {
                    $exists = AttendanceRecord::where('class_session_id', $session->id)
                        ->where('student_id', $enrollment->student_id)
                        ->exists();

                    if (! $exists) {
                        AttendanceRecord::create([
                            'class_session_id' => $session->id,
                            'student_id' => $enrollment->student_id,
                            'status' => 'status', // unmarked
                        ]);
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create session or attendance records.',
                    'error' => $e->getMessage()
                ], 500);
            }

            $session->load([
                'classSchedule',
                'calendarSchedule',
            ]);
        }

        $records = AttendanceRecord::with(['student.user:id,first_name,last_name', 'markedBy:id,first_name,last_name'])
            ->where('class_session_id', $session->id)
            ->get();

        return response()->json([
            'success' => true,
            'session' => $session,
            'attendance_records' => $records,
        ]);
    }

    /**
     * Utility: get most recent weekday on or before a given date.
     * e.g. mostRecentWeekdayOnOrBefore('2025-11-14', 'Monday') => Carbon date for that Monday
     */
    protected function mostRecentWeekdayOnOrBefore(Carbon $date, string $dayOfWeek)
    {
        $targetDay = ucfirst(strtolower($dayOfWeek)); // ensure "Monday" format
        $candidate = $date->copy();
        $limit = 14; // 2 weeks safety
        for ($i = 0; $i < $limit; $i++) {
            if (strtolower($candidate->format('l')) === strtolower($targetDay)) {
                return $candidate->startOfDay();
            }
            $candidate->subDay();
        }
        return null;
    }
}
