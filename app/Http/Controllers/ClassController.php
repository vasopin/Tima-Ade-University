<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct()
    {
        // Only admin may create, edit, update or delete classes
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isRegistrar())) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    public function index()
    {
        $classes = SchoolClass::withCount(['students', 'sections'])
            ->with('classTeacher.user')
            ->latest()
            ->paginate(15);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'sections'    => 'array',
            'sections.*'  => 'string|max:100',
        ]);

        $class = SchoolClass::create([
            'name'        => $validated['name'],
            'grade_level' => $validated['grade_level'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active'   => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['sections'])) {
            foreach ($validated['sections'] as $sectionName) {
                if (trim($sectionName)) {
                    $class->sections()->create(['name' => trim($sectionName), 'capacity' => 40]);
                }
            }
        }

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully!');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['sections', 'students.user', 'subjects', 'classTeacher.user']);
        return view('classes.show', compact('class'));
    }

    public function edit(SchoolClass $class)
    {
        $class->load('sections');
        $allSubjects    = Subject::where('is_active', true)->get();
        $classSubjectIds = $class->subjects->pluck('id')->toArray();
        return view('classes.edit', compact('class', 'allSubjects', 'classSubjectIds'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully!');
    }

    public function destroy(SchoolClass $class)
    {
        // Only admin may delete classes
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $class->delete();
        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }
}
