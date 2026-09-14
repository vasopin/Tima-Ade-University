<?php

namespace App\Http\Controllers;

use App\Models\AcademicStanding;
use App\Models\Student;
use Illuminate\Http\Request;

class AcademicStandingController extends Controller
{
    private function authorizeRegistrar(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403, 'Registrar access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeRegistrar();

        $query = Student::with(['user', 'academicStanding'])->whereHas('user');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->whereHas('academicStanding', fn ($q) => $q->where('status', $request->string('status')));
        }

        $students = $query->paginate(15)->withQueryString();
        $students->getCollection()->each(function ($student) {
            $computed = AcademicStanding::computeForStudent($student->id);
            $student->setAttribute('computed_standing', $computed);
        });

        return view('registrar.academic-standing.index', compact('students'));
    }

    public function edit(Student $student)
    {
        $this->authorizeRegistrar();

        $student->load('user');
        $standing = AcademicStanding::where('student_id', $student->id)->first();
        $computed = AcademicStanding::computeForStudent($student->id);

        return view('registrar.academic-standing.edit', compact('student', 'standing', 'computed'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorizeRegistrar();

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $computed = AcademicStanding::computeForStudent($student->id);

        AcademicStanding::updateOrCreate(
            ['student_id' => $student->id],
            [
                'status'         => $computed['status'],
                'cumulative_gpa' => $computed['cumulative_gpa'],
                'failed_courses' => $computed['failed_courses'],
                'notes'          => $validated['notes'] ?? null,
                'updated_by'     => auth()->id(),
            ]
        );

        return redirect()->route('registrar.academic-standing.index')
            ->with('success', "Academic standing for '{$student->user->name}' recalculated from real examination data and saved.");
    }
}
