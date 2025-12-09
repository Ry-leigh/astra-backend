<?php

namespace App\Http\Controllers;

use App\Models\CalendarSchedule;
use App\Models\ClassCourse;
use App\Models\Classroom;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarScheduleController extends Controller
{
    public function index() {
        $user = Auth::user();

        $roleNames = $user->roles->pluck('name')->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();

        $query = CalendarSchedule::with('targets')->latest();

        // 🟩 Admins see everything
        if (in_array('Administrator', $roleNames)) {
            $schedules = $query->get();
        } else {
            // 🟦 Non-admin users see only relevant schedules
            $schedules = $query->whereHas('targets', function ($q) use ($user, $roleIds, $roleNames) {
                $q->where(function ($q2) use ($user, $roleIds, $roleNames) {
                    // Global
                    $q2->orWhere(function ($q3) {
                        $q3->where('target_type', 'global')->whereNull('target_id');
                    });

                    // Role
                    $q2->orWhere(function ($q3) use ($roleIds) {
                        $q3->where('target_type', 'role')->whereIn('target_id', $roleIds);
                    });

                    // Program
                    if ($user->program_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'program')->where('target_id', $user->program_id);
                        });
                    }

                    // Classroom
                    if ($user->classroom_id) {
                        $q2->orWhere(function ($q3) use ($user) {
                            $q3->where('target_type', 'classroom')->where('target_id', $user->classroom_id);
                        });
                    }

                    // Courses (Instructor handled)
                    if (in_array('Instructor', $roleNames) && $user->instructor && $user->instructor->classCourses) {
                        $courseIds = $user->instructor->classCourses->pluck('id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }

                    // Courses (Student/Officer enrolled)
                    if (array_intersect($roleNames, ['Student', 'Officer']) && $user->student && $user->student->enrollments) {
                        $courseIds = $user->student->enrollments->pluck('class_course_id')->toArray();
                        $q2->orWhere(function ($q3) use ($courseIds) {
                            $q3->where('target_type', 'course')->whereIn('target_id', $courseIds);
                        });
                    }
                });
            })->get();
        }

        return response()->json(['success' => true, 'schedules' => $schedules]);
    }

    public function show($id) {
        $schedule = CalendarSchedule::with('targets')->findOrFail($id);
        return response()->json($schedule);
    }

    private function formatProgramName($str) {
        $stopWords = ["in", "of", "and"];

        // Match title + optional section digits
        preg_match('/^(.*?)(\d.*)?$/', $str, $matches);

        $title = trim($matches[1] ?? '');
        $section = isset($matches[2]) ? trim($matches[2]) : '';

        // Avoid empty splits
        $words = array_filter(preg_split('/\s+/', $title));

        // Build acronym
        $acronym = implode('', array_map(function($word) use ($stopWords) {
            return in_array(strtolower($word), $stopWords)
                ? ''
                : strtoupper($word[0]);
        }, $words));

        return trim($acronym . ($section ? ' ' . $section : ''));
    }

    public function create() {
        $user = Auth::user();

        if ($user->hasRole('Administrator')) {
            $roles = Role::select('id', 'name')->get();
            $programs = Program::select('id', 'name')->get();
            $classrooms = Classroom::join('programs', 'classrooms.program_id', '=', 'programs.id')
                ->select('classrooms.id', DB::raw("CONCAT(programs.name, ' ', classrooms.year_level, classrooms.section) as name"))
                ->get();
            $classCourses = ClassCourse::with(['course', 'classroom.program', 'instructor.user'])
                ->get()
                ->map(fn($cc) => [
                    'id' => $cc->id,
                    'name' => sprintf(
                        '%s - %s %s%s',
                        $cc->course->code,
                        // match($cc->instructor->user->sex) {
                        //     'M' => 'Sir ',
                        //     'F' => "Ma'am ",
                        //     default => '',
                        // } . $cc->instructor->user->first_name . ' ' . $cc->instructor->user->last_name,
                        $this->formatProgramName($cc->classroom->program->name),
                        $cc->classroom->year_level,
                        $cc->classroom->section
                    )
                ]);

            return response()->json([
                'success' => true,
                'roles' => $roles,
                'programs' => $programs,
                'classrooms' => $classrooms,
                'class_courses' => $classCourses,
            ]);
        }
    }

    public function store(Request $request) {
        $user = Auth::user();

        if (!$user->hasRole('Administrator')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'all_day' => 'nullable|boolean',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'category' => 'required|in:holiday,event,meeting,exam,makeup_class',
            'class_course_id' => 'nullable|exists:class_courses,id',
            'room' => 'nullable',
            'repeats' => 'nullable|in:none,daily,weekly,monthly,yearly',
            'targets' => 'required|array',
            'targets.global' => 'boolean',
            'targets.roles' => 'array',
            'targets.programs' => 'array',
            'targets.classrooms' => 'array',
            'targets.class_courses' => 'array',
        ]);    

        $schedule = CalendarSchedule::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'all_day' => $validated['all_day'] ?? false,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'category' => $validated['category'],
            'class_course_id' => $validated['class_course_id'] ?? null,
            'room' => $validated['room'] ?? null,
            'repeats' => $validated['repeats'] ?? 'none',
            'created_by' => $user->id,
        ]);

        $targets = [];
        if (!empty($validated['targets']['global'])) {
            $targets[] = ['target_type' => 'global', 'target_id' => null];
        } else {
            $mapping = [
                'roles' => 'role',
                'programs' => 'program',
                'classrooms' => 'classroom',
                'class_courses' => 'course',
            ];
            foreach ($mapping as $key => $type) {
                if (!empty($validated['targets'][$key])) {
                    foreach ($validated['targets'][$key] as $id) {
                        $targets[] = ['target_type' => $type, 'target_id' => $id];
                    }
                }
            }
        }

        $schedule->targets()->createMany($targets);

        return response()->json(['success' => true,'message' => 'Schedule created successfully.']);
    }

    public function update(Request $request, $id) {
        $user = Auth::user();
        if (!$user->roles->contains('name', 'Administrator')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $schedule = CalendarSchedule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'all_day' => 'nullable|boolean',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'category' => 'required|in:holiday,event,meeting,exam,makeup_class',
            'class_course_id' => 'nullable|exists:class_courses,id',
            'room' => 'nullable',
            'repeats' => 'nullable|in:none,daily,weekly,monthly,yearly',
            'targets' => 'required|array',
            'targets.global' => 'boolean',
            'targets.roles' => 'array',
            'targets.programs' => 'array',
            'targets.classrooms' => 'array',
            'targets.class_courses' => 'array',
        ]);

        $schedule->update([...$validated, 'last_updated_by' => $user->id]);

        if (isset($validated['targets'])) {
            $schedule->targets()->delete();
            $targets = [];
            if (!empty($validated['targets']['global'])) {
                $targets[] = ['target_type' => 'global', 'target_id' => null];
            } else {
                $mapping = [
                    'roles' => 'role',
                    'programs' => 'program',
                    'classrooms' => 'classroom',
                    'class_courses' => 'course',
                ];
                foreach ($mapping as $key => $type) {
                    if (!empty($validated['targets'][$key])) {
                        foreach ($validated['targets'][$key] as $id) {
                            $targets[] = ['target_type' => $type, 'target_id' => $id];
                        }
                    }
                }
            }
            $schedule->targets()->createMany($targets);
        }

        return response()->json(['success' => true, 'message' => 'Schedule updated successfully.']);
    }

    public function destroy($id) {
        $user = Auth::user();
        if (!$user->roles->contains('name', 'Administrator')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $schedule = CalendarSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully.']);
    }
}
