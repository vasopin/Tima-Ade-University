<?php

namespace App\Http\Controllers;

use App\Models\ExamMark;
use App\Models\FeePayment;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    private function children()
    {
        $user = auth()->user();
        abort_unless($user && $user->isParent(), 403, 'Parent access required.');

        return $user->guardian
            ? $user->guardian->students()->with(['user', 'schoolClass', 'section'])->get()
            : collect();
    }

    private function child(Request $request): ?Student
    {
        $children = $this->children();
        if ($children->isEmpty()) {
            return null;
        }

        $requestedId = (int) $request->query('student_id', $children->first()->id);
        $child = $children->firstWhere('id', $requestedId);
        abort_unless($child, 403, 'Unauthorized access to child record.');

        return $child;
    }

    public function profiles(Request $request)
    {
        $children = $this->children();
        $selectedChild = $this->child($request);
        return view('parent.profiles', compact('children', 'selectedChild'));
    }

    public function academics(Request $request)
    {
        $children = $this->children();
        $selectedChild = $this->child($request);
        $marks = $selectedChild ? ExamMark::with(['exam.subject'])->where('student_id', $selectedChild->id)->latest()->get() : collect();
        $transcripts = $selectedChild ? $selectedChild->transcripts()->latest('generated_at')->get() : collect();
        $totalPossible = $marks->sum(fn ($mark) => (float) ($mark->exam->total_marks ?? 0));
        $totalObtained = $marks->where('is_absent', false)->sum('marks_obtained');
        $gpa = $totalPossible > 0 ? round(min(4, max(0, ($totalObtained / $totalPossible) * 4)), 2) : 0;
        $standing = $selectedChild?->academicStanding;
        return view('parent.academics', compact('children', 'selectedChild', 'marks', 'transcripts', 'gpa', 'standing'));
    }

    public function finance(Request $request)
    {
        $children = $this->children();
        $selectedChild = $this->child($request);
            $payments = $selectedChild ? FeePayment::with('feeStructure')->where('student_id', $selectedChild->id)->latest('payment_date')->paginate(15)->withQueryString() : collect();
            $outstanding = $selectedChild
                ? FeePayment::with('feeStructure')->where('student_id', $selectedChild->id)
                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                    ->get()
                    ->sum(fn ($payment) => $payment->total_due - (float) $payment->amount_paid)
                : 0;
        return view('parent.finance', compact('children', 'selectedChild', 'payments', 'outstanding'));
    }

    public function communications(Request $request)
    {
        $children = $this->children();
        $selectedChild = $this->child($request);
        $user = auth()->user();
        $role = $user->role?->slug ?? 'parent';
        $notifications = Notification::where(function ($query) use ($user, $role) {
            $query->where('user_id', $user->id)->orWhere(fn ($broadcast) => $broadcast->whereNull('user_id')->whereIn('role', ['all', $role]));
        })->latest()->paginate(15)->withQueryString();
        $notices = Notice::where('is_active', true)->latest('published_date')->take(10)->get();
        return view('parent.communications', compact('children', 'selectedChild', 'notifications', 'notices'));
    }
}
