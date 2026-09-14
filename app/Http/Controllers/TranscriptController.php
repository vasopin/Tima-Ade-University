<?php

namespace App\Http\Controllers;

use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Transcript;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    private function authorizeRegistrar(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403, 'Registrar access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeRegistrar();

        $query = Transcript::with(['student.user', 'generatedBy']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('student.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $transcripts = $query->latest('generated_at')->paginate(15)->withQueryString();

        return view('registrar.transcripts.index', compact('transcripts'));
    }

    public function create()
    {
        $this->authorizeRegistrar();

        $students = Student::with('user')->whereHas('user')->get();

        return view('registrar.transcripts.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->authorizeRegistrar();

        $validated = $request->validate([
            'student_id'    => ['required', 'exists:students,id'],
            'academic_year' => ['required', 'string', 'max:50'],
            'term'          => ['required', 'string', 'max:50'],
        ]);

        $marks = ExamMark::where('student_id', $validated['student_id'])
            ->whereHas('exam.resultsApproval', fn ($query) => $query->whereIn('status', ['approved', 'published']))
            ->with('exam')->get();

        if ($marks->isEmpty()) {
            return back()->withInput()->with('error', 'This student has no recorded examination marks yet — a transcript cannot be generated from empty academic data.');
        }

        $totalObtained = 0;
        $totalPossible = 0;
        foreach ($marks as $mark) {
            $totalObtained += (float) $mark->marks_obtained;
            $totalPossible += (float) ($mark->exam->total_marks ?? 0);
        }

        $percentage = $totalPossible > 0 ? ($totalObtained / $totalPossible) * 100 : 0;
        $gpa = round(min(4.0, max(0.0, $percentage / 25)), 2);

        $subjectSummary = $marks->groupBy('exam.subject_id')->map(function ($subjectMarks) {
            $first = $subjectMarks->first();
            return sprintf(
                '%s: %s (%s%%)',
                $first->exam->subject->name ?? 'Unknown Subject',
                $first->grade,
                $first->exam->total_marks > 0 ? round(($first->marks_obtained / $first->exam->total_marks) * 100, 1) : 0
            );
        })->values()->implode("\n");

        $transcript = Transcript::create([
            'student_id'    => $validated['student_id'],
            'academic_year' => $validated['academic_year'],
            'term'          => $validated['term'],
            'gpa'           => $gpa,
            'total_credits' => $marks->count(),
            'summary'       => $subjectSummary,
            'generated_by'  => auth()->id(),
            'generated_at'  => now(),
        ]);

        return redirect()->route('registrar.transcripts.show', $transcript)
            ->with('success', 'Transcript generated successfully from the student\'s real examination history.');
    }

    public function show(Transcript $transcript)
    {
        $user = auth()->user();
        $isOwner = $user->isStudent() && $user->student?->id === $transcript->student_id;

        abort_unless($isOwner || $user->isAdmin() || $user->isRegistrar(), 403, 'Unauthorized access to this transcript.');

        $transcript->load(['student.user', 'generatedBy']);

        return view('registrar.transcripts.show', compact('transcript'));
    }

    public function myTranscripts()
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = $user->student;
        $transcripts = $student
            ? Transcript::where('student_id', $student->id)->latest('generated_at')->get()
            : collect();

        return view('registrar.transcripts.mine', compact('transcripts'));
    }
}
