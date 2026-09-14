<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isRegistrar())) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->get();
        $selectedClassId = $request->class_id ?? ($classes->first()?->id ?? null);
        $selectedSectionId = $request->section_id ?? null;

        $sections = $selectedClassId ? Section::where('school_class_id', $selectedClassId)->get() : collect();

        $query = Timetable::with(['schoolClass', 'section', 'subject', 'teacher.user'])
            ->where('is_active', true);

        if ($selectedClassId) {
            $query->where('school_class_id', $selectedClassId);
        }

        if ($selectedSectionId) {
            $query->where('section_id', $selectedSectionId);
        }

        $schedules = $query->orderBy('start_time')->get()->groupBy('day_of_week');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('timetables.index', compact('classes', 'sections', 'selectedClassId', 'selectedSectionId', 'schedules', 'days'));
    }

    public function create()
    {
        $classes  = SchoolClass::where('is_active', true)->get();
        $sections = Section::all();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::with('user')->get();
        $days     = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('timetables.create', compact('classes', 'sections', 'subjects', 'teachers', 'days'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            'subject_id'      => 'required|exists:subjects,id',
            'teacher_id'      => 'nullable|exists:teachers,id',
            'day_of_week'     => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'room_number'     => 'nullable|string|max:50',
        ]);

        Timetable::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('timetables.index', ['class_id' => $validated['school_class_id']])
            ->with('success', 'Timetable slot scheduled successfully.');
    }

    public function edit(Timetable $timetable)
    {
        $classes  = SchoolClass::where('is_active', true)->get();
        $sections = Section::where('school_class_id', $timetable->school_class_id)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::with('user')->get();
        $days     = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('timetables.edit', compact('timetable', 'classes', 'sections', 'subjects', 'teachers', 'days'));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            'subject_id'      => 'required|exists:subjects,id',
            'teacher_id'      => 'nullable|exists:teachers,id',
            'day_of_week'     => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'      => 'required',
            'end_time'        => 'required|after:start_time',
            'room_number'     => 'nullable|string|max:50',
            'is_active'       => 'nullable|boolean',
        ]);

        $timetable->update($validated);

        return redirect()->route('timetables.index', ['class_id' => $timetable->school_class_id])
            ->with('success', 'Timetable schedule updated successfully.');
    }

    public function destroy(Timetable $timetable)
    {
        $classId = $timetable->school_class_id;
        $timetable->delete();

        return redirect()->route('timetables.index', ['class_id' => $classId])
            ->with('success', 'Timetable schedule slot deleted successfully.');
    }
}
