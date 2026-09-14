<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Attendance::with(['student.user', 'schoolClass', 'section', 'markedBy']);

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        } else {
            $query->whereDate('attendance_date', today());
        }

        if ($user && $user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                $attendances = collect();
                $classes = SchoolClass::where('is_active', true)->get();
                return view('attendance.index', compact('attendances', 'classes'));
            }

            $query->where('student_id', $student->id);
            $attendances = $query->latest()->paginate(20)->withQueryString();
            $classes = SchoolClass::where('is_active', true)->get();

            return view('attendance.index', compact('attendances', 'classes'));
        }

        if ($user && $user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];

            if (empty($allowedIds)) {
                $attendances = collect();
                $classes = SchoolClass::where('is_active', true)->get();
                return view('attendance.index', compact('attendances', 'classes'));
            }

            $query->whereIn('student_id', $allowedIds);
            if ($request->filled('student_id')) {
                if (! in_array((int) $request->student_id, $allowedIds, true)) {
                    abort(403, 'Unauthorized access to student attendance.');
                }
                $query->where('student_id', $request->student_id);
            }
            $attendances = $query->latest()->paginate(20)->withQueryString();
            $classes = SchoolClass::where('is_active', true)->get();

            return view('attendance.index', compact('attendances', 'classes'));
        }

        if ($user && $user->isTeacher() && ! $user->isAdmin()) {
            $allowedClassIds = $this->authorizedTeacherClassIds($user);
            if ($request->filled('class_id')) {
                if (! in_array((int) $request->class_id, $allowedClassIds, true)) {
                    abort(403, 'You are not authorized to view attendance for this class.');
                }
                $query->where('school_class_id', $request->class_id);
            } else {
                $query->whereIn('school_class_id', $allowedClassIds);
            }

            $classes = SchoolClass::whereIn('id', $allowedClassIds)->where('is_active', true)->get();
            $attendances = $query->latest()->paginate(20)->withQueryString();

            return view('attendance.index', compact('attendances', 'classes'));
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        $attendances = $query->latest()->paginate(20)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('attendance.index', compact('attendances', 'classes'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isTeacher() || $user->isAdmin()), 403, 'Teacher or administrator access required.');
        $allowedClassIds = $user && $user->isTeacher() && ! $user->isAdmin() ? $this->authorizedTeacherClassIds($user) : SchoolClass::where('is_active', true)->pluck('id')->all();

        $classes = SchoolClass::whereIn('id', $allowedClassIds)->where('is_active', true)->get();
        $sections = [];
        $students = collect();
        $date = $request->date ?? today()->toDateString();
        $classId = $request->class_id ?? null;
        $sectionId = $request->section_id ?? null;

        if ($classId) {
            if (! in_array((int) $classId, $allowedClassIds, true)) {
                abort(403, 'You are not authorized to manage attendance for this class.');
            }
            $sections = Section::where('school_class_id', $classId)->get();
        }

        if ($classId && $sectionId) {
            $students = Student::with(['user', 'attendances' => fn($q) => $q->where('attendance_date', $date)])
                ->where('school_class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('status', 'active')
                ->get();
        }

        return view('attendance.create', compact('classes', 'sections', 'students', 'date', 'classId', 'sectionId'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isTeacher() && !auth()->user()->isAdmin())) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($query) => $query->where('school_class_id', $request->input('school_class_id'))),
            ],
            'attendance'      => 'required|array',
            'attendance.*'    => 'required|in:present,absent,late,excused',
        ]);

        if (auth()->user()->isTeacher() && ! auth()->user()->isAdmin()) {
            $allowedClassIds = $this->authorizedTeacherClassIds(auth()->user());
            if (! in_array((int) $validated['school_class_id'], $allowedClassIds, true)) {
                abort(403, 'You are not authorized to manage attendance for this class.');
            }
        }

        $saved = 0;
        foreach ($validated['attendance'] as $studentId => $status) {
            $student = Student::find($studentId);
            if (
                ! $student
                || (int) $student->school_class_id !== (int) $validated['school_class_id']
                || (int) $student->section_id !== (int) $validated['section_id']
            ) {
                continue;
            }

            $attendance = Attendance::where('student_id', $studentId)
                ->whereDate('attendance_date', $validated['attendance_date'])
                ->first() ?? new Attendance;

            $attendance->fill([
                'student_id'      => $studentId,
                'school_class_id' => $validated['school_class_id'],
                'section_id'      => $validated['section_id'],
                'marked_by'       => Auth::id(),
                'attendance_date' => $validated['attendance_date'],
                'status'          => $status,
                'remarks'         => $request->input("remarks.$studentId") ?? null,
            ])->save();
            $saved++;
        }

        return redirect()->route('attendance.index', [
            'class_id' => $validated['school_class_id'],
            'date' => $validated['attendance_date'],
        ])
            ->with('success', "Attendance saved successfully for $saved students.");
    }

    public function edit(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user && $user->isTeacher() && ! $user->isAdmin() ? $this->authorizedTeacherClassIds($user) : SchoolClass::where('is_active', true)->pluck('id')->all();

        $classes = SchoolClass::whereIn('id', $allowedClassIds)->where('is_active', true)->get();
        $sections = [];
        $students = collect();
        $date = $request->date ?? today()->toDateString();
        $classId = $request->class_id ?? null;
        $sectionId = $request->section_id ?? null;

        if ($classId) {
            if (! in_array((int) $classId, $allowedClassIds, true)) {
                abort(403, 'You are not authorized to update attendance for this class.');
            }
            $sections = Section::where('school_class_id', $classId)->get();
        }

        if ($classId && $sectionId) {
            $students = Student::with(['user', 'attendances' => fn($q) => $q->where('attendance_date', $date)])
                ->where('school_class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('status', 'active')
                ->get();
        }

        return view('attendance.edit', compact('classes', 'sections', 'students', 'date', 'classId', 'sectionId'));
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        $classes = SchoolClass::where('is_active', true)->get();
        $classId = $request->class_id;
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $report = null;

        if ($user && $user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                $report = collect();
                return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
            }

            $report = Student::with(['user', 'attendances' => fn($q) => $q
                ->whereDate('attendance_date', '>=', $startDate)
                ->whereDate('attendance_date', '<=', $endDate)
            ])
            ->where('id', $student->id)
            ->where('status', 'active')
            ->get();

            return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
        }

        if ($user && $user->isParent()) {
            $guardian = $user->guardian;
            $allowedIds = $guardian ? $guardian->students->pluck('id')->toArray() : [];

            if (empty($allowedIds)) {
                $report = collect();
                return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
            }

            $selectedStudentId = $request->student_id ?: ($allowedIds[0] ?? null);
            if ($selectedStudentId && ! in_array((int) $selectedStudentId, $allowedIds, true)) {
                abort(403, 'Unauthorized access to student report.');
            }

            $report = Student::with(['user', 'attendances' => fn($q) => $q
                ->whereDate('attendance_date', '>=', $startDate)
                ->whereDate('attendance_date', '<=', $endDate)
            ])
            ->where('id', $selectedStudentId)
            ->where('status', 'active')
            ->get();

            return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
        }

        if ($user && $user->isTeacher() && ! $user->isAdmin()) {
            $allowedClassIds = $this->authorizedTeacherClassIds($user);
            if ($classId && ! in_array((int) $classId, $allowedClassIds, true)) {
                abort(403, 'You are not authorized to view attendance for this class.');
            }
            $classId = $classId ?: ($allowedClassIds[0] ?? null);
            if ($classId) {
                $report = Student::with(['user', 'attendances' => fn($q) => $q
                    ->whereDate('attendance_date', '>=', $startDate)
                    ->whereDate('attendance_date', '<=', $endDate)
                ])
                ->where('school_class_id', $classId)
                ->where('status', 'active')
                ->get();
            }
            $classes = SchoolClass::whereIn('id', $allowedClassIds)->where('is_active', true)->get();
            return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
        }

        if ($classId) {
            $report = Student::with(['user', 'attendances' => fn($q) => $q
                ->whereDate('attendance_date', '>=', $startDate)
                ->whereDate('attendance_date', '<=', $endDate)
            ])
            ->where('school_class_id', $classId)
            ->where('status', 'active')
            ->get();
        }

        return view('attendance.report', compact('classes', 'classId', 'startDate', 'endDate', 'report'));
    }

    protected function authorizedTeacherClassIds($user): array
    {
        if (! $user || ! $user->isTeacher()) {
            return [];
        }

        $teacher = $user->teacher;
        if (! $teacher) {
            return [];
        }

        $classIds = [$teacher->class_teacher_of];
        $assignedIds = DB::table('class_subject')
            ->where('teacher_id', $teacher->user_id)
            ->pluck('school_class_id')
            ->all();

        foreach ($assignedIds as $classId) {
            $classIds[] = $classId;
        }

        return array_values(array_unique(array_filter(array_map('intval', $classIds))));
    }
}
