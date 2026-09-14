<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdmissionApplicationController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && ($user->isAdmin() || $user->isStaff() || $user->isAdmissionsOfficer()),
            403,
            'Administrative access required.'
        );
    }

    private function validatedActiveProgramId(mixed $programId): int
    {
        $programId = is_numeric($programId) ? (int) $programId : null;

        abort_unless($programId, 422, 'Select a valid active program of interest.');

        $program = Program::query()
            ->where('is_active', true)
            ->whereHas('department', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('faculty', fn ($faculty) => $faculty->where('is_active', true));
            })
            ->find($programId);

        abort_unless($program, 422, 'Select a valid active program of interest.');

        return $program->id;
    }

    public function create(Request $request)
    {
        $programs = Program::query()
            ->with('department.faculty')
            ->where('is_active', true)
            ->whereHas('department', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('faculty', fn ($faculty) => $faculty->where('is_active', true));
            })
            ->orderBy('name')
            ->get();

        return view('public.apply', [
            'step' => max(1, min(4, (int) $request->query('step', 1))),
            'programs' => $programs,
            'draft' => $request->session()->get('admission_application', []),
        ]);
    }

    public function storeBasic(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'program_id' => ['nullable', 'integer', Rule::exists('programs', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'grade_interested' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->filled('program_id')) {
            $data['program_id'] = $this->validatedActiveProgramId($data['program_id']);
            $data['grade_interested'] = Program::query()->where('is_active', true)->findOrFail($data['program_id'])->name;
        } elseif ($request->filled('grade_interested')) {
            $data['program_id'] = null;
            $data['grade_interested'] = trim((string) $data['grade_interested']);
        } else {
            abort(422, 'Select a valid active program of interest.');
        }

        $request->session()->put('admission_application.basic', $data);
        return redirect()->route('public.apply', ['step' => 2]);
    }

    public function storeEducation(Request $request)
    {
        $data = $request->validate([
            'previous_institution' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'between:1900,2100'],
        ]);
        abort_unless($request->session()->has('admission_application.basic'), 422, 'Complete Basic Information first.');
        $request->session()->put('admission_application.education', $data);
        return redirect()->route('public.apply', ['step' => 3]);
    }

    public function storeDocuments(Request $request)
    {
        abort_unless($request->session()->has('admission_application.basic'), 422, 'Complete Basic Information first.');
        abort_unless($request->session()->has('admission_application.education'), 422, 'Complete Education Information first.');
        $data = $request->validate([
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        $documents = [];
        foreach ($data['documents'] ?? [] as $document) {
            $documents[] = [
                'name' => $document->getClientOriginalName(),
                'path' => $document->store('admission-documents', 'local'),
                'status' => 'submitted',
            ];
        }
        $request->session()->put('admission_application.documents', $documents);
        return redirect()->route('public.apply', ['step' => 4]);
    }

    public function submit(Request $request)
    {
        $request->validate(['confirmation' => ['accepted']]);
        $draft = $request->session()->get('admission_application', []);
        abort_unless(is_array($draft), 422, 'Complete every application step first.');
        abort_unless(isset($draft['basic'], $draft['education'], $draft['documents']), 422, 'Complete every application step first.');

        $basic = $draft['basic'];
        abort_unless(is_array($basic), 422, 'Complete Basic Information first.');

        $basic = Validator::make($basic, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'program_id' => ['nullable', 'integer', Rule::exists('programs', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'grade_interested' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        if ($basic['program_id'] ?? null) {
            $basic['program_id'] = $this->validatedActiveProgramId($basic['program_id']);
            $basic['grade_interested'] = Program::query()->where('is_active', true)->findOrFail($basic['program_id'])->name;
        } else {
            $basic['program_id'] = null;
            $basic['grade_interested'] = trim((string) ($basic['grade_interested'] ?? ''));
            abort_unless($basic['grade_interested'] !== '', 422, 'Select a valid active program of interest.');
        }

        abort_unless(is_array($draft['education']), 422, 'Complete Education Information first.');
        abort_unless(is_array($draft['documents']), 422, 'Complete Documents step first.');

        $draft['basic'] = $basic;
        $request->session()->put('admission_application', $draft);

        $application = AdmissionApplication::create(array_merge($basic, ['education' => $draft['education'], 'documents' => $draft['documents']]));
        $request->session()->forget('admission_application');
        return view('public.apply-success', compact('application'));
    }

    public function index(Request $request, string $viewName = 'admin.applications.index')
    {
        $this->authorizeAdmin();
        $view = (string) $request->input('view', '') ?: 'applications';
        $sort = $request->input('sort') === 'oldest' ? 'asc' : 'desc';
        $documentStatus = (string) $request->input('document_status', '');

        $applications = AdmissionApplication::query()
            ->when($request->filled('search'), function ($query) use ($request, $view) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($query) use ($search, $view) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                    if ($view === 'documents') {
                        $query->orWhere('documents', 'like', "%{$search}%");
                    }
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->input('status')))
            ->when($request->filled('submitted_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('submitted_from')))
            ->when($request->filled('submitted_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('submitted_to')))
            ->when($documentStatus === 'missing', function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('documents')
                        ->orWhere('documents', '[]')
                        ->orWhere('documents', '');
                });
            })
            ->when(in_array($documentStatus, ['submitted', 'verified', 'requires_correction'], true), fn ($query) => $query->where('documents', 'like', '%"status":"' . $documentStatus . '"%'))
            ->when($view === 'documents', fn ($query) => $query->whereNotNull('documents')->where('documents', '!=', '[]'))
            ->when($view === 'reviews', fn ($query) => $query->whereIn('status', ['submitted', 'under_review']))
            ->when($view === 'decisions', fn ($query) => $query->whereIn('status', ['under_review', 'approved', 'declined']))
            ->orderBy('created_at', $sort)
            ->paginate(15)
            ->withQueryString();

        $documentStats = [
            'total' => 0,
            'pending_review' => 0,
            'verified' => 0,
            'rejected' => 0,
            'missing' => 0,
        ];

        foreach (AdmissionApplication::query()->get() as $application) {
            $documents = is_array($application->documents) ? $application->documents : [];
            if (empty($documents)) {
                $documentStats['missing']++;
                continue;
            }

            $documentStats['total'] += count($documents);

            foreach ($documents as $document) {
                $status = (string) ($document['status'] ?? 'submitted');

                if ($status === 'verified') {
                    $documentStats['verified']++;
                    continue;
                }

                if ($status === 'requires_correction') {
                    $documentStats['rejected']++;
                    continue;
                }

                $documentStats['pending_review']++;
            }
        }

        $stats = [
            'total' => AdmissionApplication::count(),
            'submitted' => AdmissionApplication::where('status', 'submitted')->count(),
            'under_review' => AdmissionApplication::where('status', 'under_review')->count(),
            'documents_pending' => AdmissionApplication::where('documents', 'like', '%"status":"submitted"%')->count(),
            'interviews_pending' => 0,
            'approved' => AdmissionApplication::where('status', 'approved')->count(),
            'declined' => AdmissionApplication::where('status', 'declined')->count(),
        ];

        return view($viewName, compact('applications', 'view', 'stats', 'sort', 'documentStatus', 'documentStats'));
    }

    public function reports(Request $request)
    {
        $this->authorizeAdmin();
        $status = $request->string('status')->toString();
        $grade = $request->string('grade')->toString();
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $filtered = AdmissionApplication::query()
            ->when(in_array($status, ['submitted', 'under_review', 'approved', 'declined'], true), fn ($query) => $query->where('status', $status))
            ->when($grade !== '', fn ($query) => $query->where('grade_interested', $grade))
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo));

        $stats = [
            'total' => (clone $filtered)->count(),
            'applicants' => (clone $filtered)->distinct('email')->count('email'),
            'pending_reviews' => (clone $filtered)->whereIn('status', ['submitted', 'under_review'])->count(),
            'completed_decisions' => (clone $filtered)->whereIn('status', ['approved', 'declined'])->count(),
            'documents_pending' => (clone $filtered)->where('documents', 'like', '%"status":"submitted"%')->count(),
        ];

        $byStatus = (clone $filtered)
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
        $byGrade = (clone $filtered)
            ->select('grade_interested')
            ->selectRaw('count(*) as total')
            ->groupBy('grade_interested')
            ->orderByDesc('total')
            ->get();
        $byDecision = (clone $filtered)
            ->whereIn('status', ['approved', 'declined'])
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
        $applications = (clone $filtered)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.applications.reports', compact('stats', 'byStatus', 'byGrade', 'byDecision', 'applications', 'status', 'grade'));
    }

    public function communications(Request $request)
    {
        $this->authorizeAdmin();
        $applications = AdmissionApplication::query()
            ->select(['id', 'reference', 'name', 'email', 'status'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $value = trim((string) $request->input('search'));
                $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('reference', 'like', "%{$value}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.communications.index', compact('applications'));
    }

    public function sendCommunication(Request $request, AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::raw($data['message'], function ($mail) use ($application, $data) {
            $mail->to($application->email, $application->name)->subject($data['subject']);
        });

        return back()->with('success', "Communication sent to {$application->name}.");
    }

    public function decisionLetter(AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($application->status, ['approved', 'declined'], true), 404);

        return view('admin.applications.decision-letter', compact('application'));
    }

    public function documents(Request $request)
    {
        $this->authorizeAdmin();
        $request->query->set('view', 'documents');

        return $this->index($request);
    }

    public function reviews(Request $request)
    {
        $this->authorizeAdmin();
        $request->query->set('view', 'reviews');

        $response = $this->index($request, 'admin.reviews.index');
        $reviewStats = [
            'total' => AdmissionApplication::whereIn('status', ['submitted', 'under_review'])->count(),
            'submitted' => AdmissionApplication::where('status', 'submitted')->count(),
            'under_review' => AdmissionApplication::where('status', 'under_review')->count(),
        ];

        $response->with('reviewStats', $reviewStats);

        return $response;
    }

    public function interviews()
    {
        $this->authorizeAdmin();

        return view('admin.interviews.index', [
            'interviewStats' => [
                'total' => 0,
                'scheduled' => 0,
                'today' => 0,
                'upcoming' => 0,
                'completed' => 0,
                'pending' => 0,
                'cancelled' => 0,
            ],
        ]);
    }

    public function decisions(Request $request)
    {
        $this->authorizeAdmin();

        $status = $request->string('status')->toString();
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');
        $search = trim($request->string('search')->toString());

        $applications = AdmissionApplication::query()
            ->with('decider')
            ->whereIn('status', ['under_review', 'approved', 'declined'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('grade_interested', 'like', "%{$search}%")
                        ->orWhereHas('decider', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, ['under_review', 'approved', 'declined'], true), fn ($query) => $query->where('status', $status))
            ->when($dateFrom, fn ($query) => $query->whereDate('decided_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('decided_at', '<=', $dateTo))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.decisions.index', [
            'applications' => $applications,
            'decisionStats' => [
                'total' => AdmissionApplication::whereIn('status', ['approved', 'declined'])->count(),
                'pending' => AdmissionApplication::where('status', 'under_review')->count(),
                'approved' => AdmissionApplication::where('status', 'approved')->count(),
                'declined' => AdmissionApplication::where('status', 'declined')->count(),
                'requiring_action' => AdmissionApplication::where('status', 'under_review')->count(),
            ],
            'status' => $status,
        ]);
    }

    public function applicants(Request $request)
    {
        $this->authorizeAdmin();
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $applicants = AdmissionApplication::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['submitted', 'under_review', 'approved', 'declined'], true), fn ($query) => $query->where('status', $status))
            ->when($request->filled('submitted_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('submitted_from')))
            ->when($request->filled('submitted_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('submitted_to')))
            ->selectRaw('MAX(id) as representative_id, email, MAX(name) as name, MAX(phone) as phone, COUNT(*) as application_count, MAX(created_at) as latest_application_at, MAX(updated_at) as last_updated')
            ->groupBy('email')
            ->orderByDesc('last_updated')
            ->paginate(15)
            ->withQueryString();

        $applicants->getCollection()->transform(function ($applicant) {
            $applicant->latest_status = AdmissionApplication::where('email', $applicant->email)->latest('created_at')->value('status');
            return $applicant;
        });

        return view('admin.applicants.index', compact('applicants', 'status'));
    }

    public function applicant(AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        $applications = AdmissionApplication::where('email', $application->email)->latest()->get();
        $documentSummary = [
            'submitted' => $applications->sum(fn ($record) => collect($record->documents ?? [])->where('status', 'submitted')->count()),
            'verified' => $applications->sum(fn ($record) => collect($record->documents ?? [])->where('status', 'verified')->count()),
            'requires_correction' => $applications->sum(fn ($record) => collect($record->documents ?? [])->where('status', 'requires_correction')->count()),
        ];
        $decision = $applications->first(fn ($record) => $record->decided_at !== null);
        return view('admin.applicants.show', compact('application', 'applications', 'documentSummary', 'decision'));
    }

    public function show(AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        return view('admin.applications.show', compact('application'));
    }

    public function document(AdmissionApplication $application, int $document)
    {
        $this->authorizeAdmin();
        $file = $application->documents[$document] ?? null;
        abort_unless(is_array($file) && !empty($file['path']) && Storage::disk('local')->exists($file['path']), 404);

        if (request()->boolean('view')) {
            return response()->file(Storage::disk('local')->path($file['path']), [
                'Content-Disposition' => 'inline; filename="' . addslashes($file['name'] ?? 'admission-document') . '"',
            ]);
        }

        return Storage::disk('local')->download($file['path'], $file['name'] ?? 'admission-document');
    }

    public function updateDocument(Request $request, AdmissionApplication $application, int $document)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'status' => ['required', 'in:submitted,verified,requires_correction'],
            'verification_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $documents = $application->documents ?? [];
        abort_unless(isset($documents[$document]) && is_array($documents[$document]), 404);
        $documents[$document]['status'] = $data['status'];
        $documents[$document]['verification_notes'] = $data['verification_notes'] ?? null;
        $documents[$document]['verified_by'] = auth()->id();
        $documents[$document]['verified_at'] = now()->toISOString();
        $application->update(['documents' => $documents]);
        return back()->with('success', 'Document verification status updated.');
    }

    public function review(AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($application->status, ['submitted', 'under_review'], true), 422, 'Only submitted applications can enter review.');
        $application->update(['status' => 'under_review']);
        return back()->with('success', 'Application moved to under review.');
    }

    public function approve(AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($application->status, ['submitted', 'under_review'], true), 422, 'Only submitted or under-review applications can be approved.');
        $application->update(['status' => 'approved', 'decided_by' => auth()->id(), 'decided_at' => now(), 'decision_reason' => null]);
        return back()->with('success', 'Application approved and preserved in the application register.');
    }

    public function decline(Request $request, AdmissionApplication $application)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($application->status, ['submitted', 'under_review'], true), 422, 'Only submitted or under-review applications can be declined.');
        $data = $request->validate(['decision_reason' => ['required', 'string', 'max:2000']]);
        $application->update(['status' => 'declined', 'decided_by' => auth()->id(), 'decided_at' => now(), 'decision_reason' => $data['decision_reason']]);
        return back()->with('success', 'Application declined and preserved with the decision reason.');
    }
}
