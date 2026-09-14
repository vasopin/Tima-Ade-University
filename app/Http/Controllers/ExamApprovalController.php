<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResultsApproval;
use Illuminate\Http\Request;

class ExamApprovalController extends Controller
{
    private function authorizeRegistrar(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403, 'Registrar access required.');
    }

    public function index(Request $request)
    {
        $this->authorizeRegistrar();

        $query = Exam::where('status', 'completed')
            ->with(['schoolClass', 'subject', 'resultsApproval.approvedBy']);

        if ($request->filled('status')) {
            $status = $request->string('status');
            if ($status === 'pending') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('resultsApproval')
                        ->orWhereHas('resultsApproval', fn ($q2) => $q2->where('status', 'pending'));
                });
            } else {
                $query->whereHas('resultsApproval', fn ($q) => $q->where('status', $status));
            }
        }

        $exams = $query->withCount('marks')->latest('exam_date')->paginate(15)->withQueryString();
        $exams->getCollection()->each(function ($exam) use ($exams) {
            $exam->setAttribute('schedule_conflicts', Exam::where('id', '<>', $exam->id)
                ->where('exam_date', $exam->exam_date?->toDateString())
                ->where('start_time', $exam->start_time)
                ->where('status', '!=', 'cancelled')
                ->count());
        });

        return view('registrar.exam-approvals.index', compact('exams'));
    }

    public function approve(Request $request, Exam $exam)
    {
        $this->authorizeRegistrar();

        abort_unless($exam->status === 'completed' && in_array($exam->workflow_status, ['submitted', 'draft'], true), 422, 'Only submitted completed exams can have results approved.');
        abort_if($exam->marks()->count() === 0, 422, 'This exam has no recorded marks to approve.');

        ExamResultsApproval::updateOrCreate(
            ['exam_id' => $exam->id],
            [
                'approved_by' => auth()->id(),
                'status'      => 'approved',
                'notes'       => $request->input('notes'),
                'approved_at' => now(),
                'published_at' => null,
                'published_by' => null,
            ]
        );
        $exam->update(['workflow_status' => 'approved']);

        return back()->with('success', "Results for '{$exam->name}' approved. Students may now view their marks.");
    }

    public function publish(Exam $exam)
    {
        $this->authorizeRegistrar();
        abort_unless($exam->resultsApproval?->status === 'approved', 422, 'Only approved results can be published.');
        $exam->resultsApproval->update(['published_at' => now(), 'published_by' => auth()->id()]);
        $exam->update(['workflow_status' => 'published', 'published_at' => now()]);
        return back()->with('success', 'Results published to eligible students.');
    }

    public function reject(Request $request, Exam $exam)
    {
        $this->authorizeRegistrar();

        abort_unless($exam->status === 'completed', 422, 'Only completed exams can have results rejected.');
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        ExamResultsApproval::updateOrCreate(
            ['exam_id' => $exam->id],
            [
                'approved_by' => auth()->id(),
                'status'      => 'rejected',
                'notes'       => $validated['notes'],
                'approved_at' => now(),
            ]
        );
        $exam->update(['workflow_status' => 'rejected']);

        return back()->with('success', "Results for '{$exam->name}' rejected and sent back for review.");
    }
}
