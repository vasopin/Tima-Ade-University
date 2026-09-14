<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct()
    {
        // Only admin may create, edit, update or delete subjects
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isRegistrar())) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    public function index()
    {
        $subjects = Subject::withCount('classes')->latest()->paginate(15);
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->get();
        return view('subjects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        Subject::create($validated);
        return redirect()->route('subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $subject->update($validated);
        return redirect()->route('subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        // Only admin may delete subjects
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $subject->delete();
        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }
}
