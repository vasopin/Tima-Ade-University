<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\Event;
use App\Models\LectureVideo;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Notice;
use App\Models\Inquiry;
use App\Models\LiveClass;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use App\Models\Guardian;
use App\Models\AdmissionApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\AdvisorAssignment;
use App\Models\GraduationRecord;
use App\Models\Scholarship;
use App\Models\ExamResultsApproval;
use App\Models\AcademicStanding;
use App\Models\AdvisingAppointment;
use App\Http\Controllers\AdvisingController;

class DashboardController extends Controller
{
    public function welcome()
    {
        return $this->renderWelcomePage();
    }

    public function studentWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function teacherWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function parentWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function staffWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function executiveWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function deanWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function departmentHeadWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function academicAdvisorWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function admissionsOfficerWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function financeOfficerWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function registrarWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function hrOfficerWelcome()
    {
        return $this->renderWelcomePage();
    }

    public function librarianWelcome()
    {
        return $this->renderWelcomePage();
    }

    private function renderWelcomePage()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return redirect()->route($user->isSuperAdmin() ? 'dashboard' : 'hemis');
        }

        $expectedRoute = $this->resolveRoleWelcomeRouteName($user);
        $currentRoute = request()->route()?->getName();

        if ($currentRoute && $currentRoute !== $expectedRoute) {
            return redirect()->route($expectedRoute);
        }

        return view('dashboard.role-welcome', [
            'user' => $user,
            'roleProfile' => $this->resolveRoleProfile($user),
            'dashboardRoute' => $this->resolveDashboardRoute($user),
        ]);
    }

    private function resolveDashboardRoute(User $user): string
    {
        if ($user->isStudent()) {
            return route('student.dashboard');
        }

        return route('dashboard');
    }

    private function resolveRoleWelcomeRouteName(User $user): string
    {
        if ($user->isStudent()) {
            return 'student.welcome';
        }

        if ($user->isTeacher()) {
            return 'teacher.welcome';
        }

        if ($user->isParent()) {
            return 'parent.welcome';
        }

        if ($user->isStaff()) {
            return 'staff.welcome';
        }

        if ($user->isExecutive()) {
            return 'executive.welcome';
        }

        if ($user->isDean()) {
            return 'dean.welcome';
        }

        if ($user->isDepartmentHead()) {
            return 'department-head.welcome';
        }

        if ($user->isAcademicAdvisor()) {
            return 'academic-advisor.welcome';
        }

        if ($user->isAdmissionsOfficer()) {
            return 'admissions_officer.welcome';
        }

        if ($user->isFinanceOfficer()) {
            return 'finance_officer.welcome';
        }

        if ($user->isRegistrar()) {
            return 'registrar.welcome';
        }

        if ($user->isHROfficer()) {
            return 'hr_officer.welcome';
        }

        if ($user->isLibrarian()) {
            return 'librarian.welcome';
        }

        return 'dashboard.welcome';
    }

    private function resolveRoleProfile(User $user): array
    {
        $roleProfiles = [
            'president' => [
                'eyebrow' => 'University Leadership',
                'title' => "Welcome to the President's Office",
                'headline' => 'Lead the future of Tima-Ade with confidence.',
                'description' => "Monitor the university's momentum across academics, operations, research, and institutional strategy from a single polished command center.",
                'badge' => 'Executive Overview',
                'stat' => 'Institutional Strategy',
                'accent' => '#0f172a',
                'soft' => '#e2e8f0',
                'surface' => '#f8fafc',
                'highlights' => [
                    ['icon' => 'bi bi-building', 'label' => 'Academic health', 'value' => 'Institution-wide view'],
                    ['icon' => 'bi bi-graph-up-arrow', 'label' => 'Performance', 'value' => 'Trend analysis'],
                    ['icon' => 'bi bi-people-fill', 'label' => 'Leadership', 'value' => 'Decision-ready insight'],
                    ['icon' => 'bi bi-shield-check', 'label' => 'Governance', 'value' => 'Policy oversight'],
                ],
            ],
            'chancellor' => [
                'eyebrow' => "Chancellor's Desk",
                'title' => "Welcome to the Chancellor's View",
                'headline' => "Steward the university's mission and momentum.",
                'description' => 'Review strategic direction, institutional indicators, and high-level academic outcomes before progressing into the operating dashboard.',
                'badge' => 'Executive Insight',
                'stat' => 'Mission Alignment',
                'accent' => '#312e81',
                'soft' => '#ddd6fe',
                'surface' => '#f5f3ff',
                'highlights' => [
                    ['icon' => 'bi bi-bank', 'label' => 'Campus focus', 'value' => 'Strategic oversight'],
                    ['icon' => 'bi bi-pie-chart-fill', 'label' => 'Portfolio', 'value' => 'Data in context'],
                    ['icon' => 'bi bi-lightbulb-fill', 'label' => 'Innovation', 'value' => 'Future-ready planning'],
                    ['icon' => 'bi bi-trophy-fill', 'label' => 'Impact', 'value' => 'Outcome tracking'],
                ],
            ],
            'dean' => [
                'eyebrow' => 'Faculty Leadership',
                'title' => 'Welcome, Dean',
                'headline' => 'Your faculty is ready for the next academic milestone.',
                'description' => 'Access department performance, student outcomes, staffing alignment, and academic planning in one trusted workspace.',
                'badge' => 'Faculty Command',
                'stat' => 'Academic Direction',
                'accent' => '#0f766e',
                'soft' => '#ccfbf1',
                'surface' => '#ecfeff',
                'highlights' => [
                    ['icon' => 'bi bi-journal-bookmark-fill', 'label' => 'Programs', 'value' => 'Curriculum watch'],
                    ['icon' => 'bi bi-people-fill', 'label' => 'Faculty', 'value' => 'Staff visibility'],
                    ['icon' => 'bi bi-bar-chart-line-fill', 'label' => 'Insights', 'value' => 'Quality trends'],
                    ['icon' => 'bi bi-mortarboard-fill', 'label' => 'Outcomes', 'value' => 'Student success'],
                ],
            ],
            'department_head' => [
                'eyebrow' => 'Department Leadership',
                'title' => 'Welcome, Department Head',
                'headline' => 'Coordinate your department with clarity and momentum.',
                'description' => 'Review teaching loads, student performance, course coverage, and academic service delivery before opening your operational dashboard.',
                'badge' => 'Department Pulse',
                'stat' => 'Operational Clarity',
                'accent' => '#0369a1',
                'soft' => '#bae6fd',
                'surface' => '#f0f9ff',
                'highlights' => [
                    ['icon' => 'bi bi-clipboard-data', 'label' => 'Reviews', 'value' => 'Progress tracking'],
                    ['icon' => 'bi bi-book-half', 'label' => 'Courses', 'value' => 'Academic coverage'],
                    ['icon' => 'bi bi-graph-up', 'label' => 'Performance', 'value' => 'Department metrics'],
                    ['icon' => 'bi bi-calendar3', 'label' => 'Planning', 'value' => 'Academic calendar'],
                ],
            ],
            'academic_advisor' => [
                'eyebrow' => 'Student Success',
                'title' => 'Welcome, Academic Advisor',
                'headline' => 'Guide students toward timely milestones and stronger outcomes.',
                'description' => 'Review advising priorities, monitor student progress, and transition seamlessly into the advising workspace.',
                'badge' => 'Advising Console',
                'stat' => 'Student Support',
                'accent' => '#7c3aed',
                'soft' => '#e9d5ff',
                'surface' => '#faf5ff',
                'highlights' => [
                    ['icon' => 'bi bi-people', 'label' => 'Advising', 'value' => 'Student load'],
                    ['icon' => 'bi bi-check2-circle', 'label' => 'Progress', 'value' => 'Milestone tracking'],
                    ['icon' => 'bi bi-chat-left-text', 'label' => 'Communication', 'value' => 'Support planning'],
                    ['icon' => 'bi bi-award', 'label' => 'Outcomes', 'value' => 'Success coaching'],
                ],
            ],
            'admissions_officer' => [
                'eyebrow' => 'Admissions Office',
                'title' => 'Welcome, Admissions Officer',
                'headline' => 'Every applicant journey starts here.',
                'description' => 'Manage applications, review readiness, and move candidates through the admissions pipeline with confidence and clarity.',
                'badge' => 'Enrollment Pipeline',
                'stat' => 'Application Flow',
                'accent' => '#c2410c',
                'soft' => '#fed7aa',
                'surface' => '#fff7ed',
                'highlights' => [
                    ['icon' => 'bi bi-file-earmark-text', 'label' => 'Applications', 'value' => 'Pipeline status'],
                    ['icon' => 'bi bi-person-plus', 'label' => 'Prospects', 'value' => 'Qualified leads'],
                    ['icon' => 'bi bi-calendar2-week', 'label' => 'Cycle', 'value' => 'Admissions timeline'],
                    ['icon' => 'bi bi-check-square', 'label' => 'Decisions', 'value' => 'Review board'],
                ],
            ],
            'finance_officer' => [
                'eyebrow' => 'Finance Management',
                'title' => 'Welcome, Finance Officer',
                'headline' => 'Keep financial operations transparent and on track.',
                'description' => 'Monitor fees, budgets, payment activity, and institutional financial performance before moving into the finance dashboard.',
                'badge' => 'Financial Overview',
                'stat' => 'Revenue Health',
                'accent' => '#047857',
                'soft' => '#bbf7d0',
                'surface' => '#f0fdf4',
                'highlights' => [
                    ['icon' => 'bi bi-cash-stack', 'label' => 'Cash flow', 'value' => 'Payment tracking'],
                    ['icon' => 'bi bi-graph-up-arrow', 'label' => 'Budget', 'value' => 'Trend review'],
                    ['icon' => 'bi bi-receipt', 'label' => 'Receivables', 'value' => 'Fee oversight'],
                    ['icon' => 'bi bi-shield-check', 'label' => 'Controls', 'value' => 'Policy compliance'],
                ],
            ],
            'registrar' => [
                'eyebrow' => 'Records & Registration',
                'title' => 'Welcome, Registrar',
                'headline' => 'Manage academic records with accuracy and confidence.',
                'description' => 'Review records, enrollment activity, degree progress, and academic compliance before entering the registrar workspace.',
                'badge' => 'Records Command',
                'stat' => 'Academic Records',
                'accent' => '#1d4ed8',
                'soft' => '#dbeafe',
                'surface' => '#eff6ff',
                'highlights' => [
                    ['icon' => 'bi bi-person-vcard', 'label' => 'Students', 'value' => 'Records status'],
                    ['icon' => 'bi bi-journal-text', 'label' => 'Enrollment', 'value' => 'Registration track'],
                    ['icon' => 'bi bi-file-earmark-check', 'label' => 'Compliance', 'value' => 'Academic records'],
                    ['icon' => 'bi bi-bar-chart', 'label' => 'Reports', 'value' => 'Operational insight'],
                ],
            ],
            'hr_officer' => [
                'eyebrow' => 'People Operations',
                'title' => 'Welcome, HR Officer',
                'headline' => 'Support a healthy, engaged, and productive campus community.',
                'description' => 'Review staffing workflows, leave requests, employee records, and workforce operations before opening the HR dashboard.',
                'badge' => 'HR Workspace',
                'stat' => 'Workforce Health',
                'accent' => '#be185d',
                'soft' => '#fbcfe8',
                'surface' => '#fdf2f8',
                'highlights' => [
                    ['icon' => 'bi bi-people-fill', 'label' => 'People', 'value' => 'Staff overview'],
                    ['icon' => 'bi bi-calendar3', 'label' => 'Leave', 'value' => 'Request tracking'],
                    ['icon' => 'bi bi-person-badge', 'label' => 'Profiles', 'value' => 'Employee records'],
                    ['icon' => 'bi bi-heart-pulse', 'label' => 'Support', 'value' => 'Workplace care'],
                ],
            ],
            'librarian' => [
                'eyebrow' => 'Library Services',
                'title' => 'Welcome, Librarian',
                'headline' => 'Your library is ready to support discovery and learning.',
                'description' => 'Review borrowing activity, materials access, reservations, and service demand before entering the library dashboard.',
                'badge' => 'Library Desk',
                'stat' => 'Knowledge Access',
                'accent' => '#7c3aed',
                'soft' => '#ddd6fe',
                'surface' => '#f5f3ff',
                'highlights' => [
                    ['icon' => 'bi bi-book', 'label' => 'Collection', 'value' => 'Resource access'],
                    ['icon' => 'bi bi-arrow-repeat', 'label' => 'Borrowing', 'value' => 'Circulation flow'],
                    ['icon' => 'bi bi-clock-history', 'label' => 'Demand', 'value' => 'Service tracking'],
                    ['icon' => 'bi bi-chat-left-text', 'label' => 'Support', 'value' => 'Student help'],
                ],
            ],
            'teacher' => [
                'eyebrow' => 'Teaching Excellence',
                'title' => 'Welcome, Teacher',
                'headline' => 'Your classroom experience is ready for a strong start today.',
                'description' => 'Access your classes, course materials, assessments, and student touchpoints before moving into the teaching dashboard.',
                'badge' => 'Faculty Workspace',
                'stat' => 'Instructional Readiness',
                'accent' => '#0f766e',
                'soft' => '#ccfbf1',
                'surface' => '#ecfeff',
                'highlights' => [
                    ['icon' => 'bi bi-journal-bookmark-fill', 'label' => 'Courses', 'value' => 'Class delivery'],
                    ['icon' => 'bi bi-people-fill', 'label' => 'Students', 'value' => 'Learning support'],
                    ['icon' => 'bi bi-file-earmark-text', 'label' => 'Assessments', 'value' => 'Evaluation flow'],
                    ['icon' => 'bi bi-bar-chart', 'label' => 'Progress', 'value' => 'Performance view'],
                ],
            ],
            'student' => [
                'eyebrow' => 'Student Portal',
                'title' => 'Welcome, Student',
                'headline' => 'Your academic journey is ready to begin.',
                'description' => 'Review your classes, assignments, progress, and academic support tools before opening the student dashboard.',
                'badge' => 'Personal Learning Hub',
                'stat' => 'Student Success',
                'accent' => '#0f766e',
                'soft' => '#d1fae5',
                'surface' => '#ecfdf5',
                'highlights' => [
                    ['icon' => 'bi bi-journal-bookmark-fill', 'label' => 'Courses', 'value' => 'Current classes'],
                    ['icon' => 'bi bi-list-task', 'label' => 'Assignments', 'value' => 'Task tracking'],
                    ['icon' => 'bi bi-graph-up', 'label' => 'Progress', 'value' => 'Academic view'],
                    ['icon' => 'bi bi-calendar-check', 'label' => 'Attendance', 'value' => 'Engagement status'],
                ],
            ],
            'parent' => [
                'eyebrow' => 'Parent Engagement',
                'title' => 'Welcome, Parent',
                'headline' => "Stay connected to your child's progress and wellbeing.",
                'description' => "Review academic updates, communicate with the school, and follow your child's growth through the parent portal.",
                'badge' => 'Family Portal',
                'stat' => 'Student Visibility',
                'accent' => '#7c2d12',
                'soft' => '#fed7aa',
                'surface' => '#fff7ed',
                'highlights' => [
                    ['icon' => 'bi bi-people-fill', 'label' => 'Child', 'value' => 'Progress overview'],
                    ['icon' => 'bi bi-journal-text', 'label' => 'Academics', 'value' => 'Current results'],
                    ['icon' => 'bi bi-chat-dots', 'label' => 'Communication', 'value' => 'School updates'],
                    ['icon' => 'bi bi-wallet2', 'label' => 'Finance', 'value' => 'Fee updates'],
                ],
            ],
            'staff' => [
                'eyebrow' => 'Staff Workspace',
                'title' => 'Welcome, Staff Member',
                'headline' => 'Your day-to-day university operations are in good hands.',
                'description' => 'Access internal tasks, support workflows, and operational tools that keep the campus running smoothly.',
                'badge' => 'Operations Hub',
                'stat' => 'Service Delivery',
                'accent' => '#475569',
                'soft' => '#e2e8f0',
                'surface' => '#f8fafc',
                'highlights' => [
                    ['icon' => 'bi bi-briefcase-fill', 'label' => 'Operations', 'value' => 'Daily tasks'],
                    ['icon' => 'bi bi-clock-history', 'label' => 'Time', 'value' => 'Attendance tracking'],
                    ['icon' => 'bi bi-headset', 'label' => 'Support', 'value' => 'Service requests'],
                    ['icon' => 'bi bi-grid-3x3-gap', 'label' => 'Tools', 'value' => 'Department systems'],
                ],
            ],
        ];

        $slug = match (true) {
            $user->isPresident() => 'president',
            $user->isChancellor() => 'chancellor',
            $user->isDean() => 'dean',
            $user->isDepartmentHead() => 'department_head',
            $user->isAcademicAdvisor() => 'academic_advisor',
            $user->isAdmissionsOfficer() => 'admissions_officer',
            $user->isFinanceOfficer() => 'finance_officer',
            $user->isRegistrar() => 'registrar',
            $user->isHROfficer() => 'hr_officer',
            $user->isLibrarian() => 'librarian',
            $user->isTeacher() => 'teacher',
            $user->isStudent() => 'student',
            $user->isParent() => 'parent',
            $user->isStaff() => 'staff',
            default => 'staff',
        };

        return $roleProfiles[$slug] ?? $roleProfiles['staff'];
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isDean()) {
            return $this->deanDashboard($user);
        }

        if ($user->isDepartmentHead()) {
            return $this->departmentHeadDashboard($user);
        }

        if ($user->isExecutive()) {
            return $this->executiveDashboard($user);
        }

        if ($user->isAcademicAdvisor()) {
            return app(AdvisingController::class)->dashboard(request());
        }

        // Specialized Officer Dashboards
        if ($user->isAdmissionsOfficer()) {
            return $this->admissionsOfficerDashboard($user);
        }

        if ($user->isFinanceOfficer()) {
            return $this->financeOfficerDashboard($user);
        }
        if ($user->isRegistrar()) {
            return $this->registrarDashboard($user);
        }
        if ($user->isHROfficer()) {
            return $this->hrOfficerDashboard($user);
        }
        if ($user->isLibrarian()) {
            return $this->librarianDashboard($user);
        }

        // Role-specific Regular Dashboards
        if ($user->isTeacher()) {
            return $this->teacherDashboard($user);
        }
        if ($user->isStudent()) {
            return $this->studentDashboard($user);
        }
        if ($user->isParent()) {
            return $this->parentDashboard($user);
        }
        if ($user->isStaff()) {
            return $this->staffDashboard($user);
        }

        // Admin / Executive Dashboard (Default)
        return $this->adminDashboard($user);
    }

    protected function executiveDashboard($user)
    {
        $year = AcademicYear::find(request()->integer('academic_year_id'))
            ?? AcademicYear::where('is_current', true)->first();
        $term = Term::find(request()->integer('term_id'))
            ?? $year?->terms()->where('is_current', true)->first();
        $from = request()->date('from')?->startOfDay() ?? now()->subDays(30)->startOfDay();
        $to = request()->date('to')?->endOfDay() ?? now()->endOfDay();
        $applications = AdmissionApplication::query();
        $totalApplications = (clone $applications)->count();
        $approvedApplications = (clone $applications)->where('status', 'approved')->count();
        $attendance = Attendance::whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]);
        $attendanceTotal = (clone $attendance)->count();
        $attendancePresent = (clone $attendance)->where('status', 'present')->count();
        $payments = FeePayment::whereBetween('payment_date', [$from->toDateString(), $to->toDateString()]);
        $marks = ExamMark::whereBetween('created_at', [$from, $to])->whereNotNull('marks_obtained');

        $stats = [
            'total_students' => Student::count(),
            'active_enrollments' => Enrollment::whereIn('status', Enrollment::ACTIVE_STATUSES)->when($term, fn ($q) => $q->whereHas('courseSection', fn ($s) => $s->where('term_id', $term->id)))->count(),
            'applicants' => $totalApplications,
            'admissions_conversion' => $totalApplications ? round(($approvedApplications / $totalApplications) * 100, 1) : 0,
            'faculty_staff' => Teacher::count() + Employee::count(),
            'active_programs' => Program::where('is_active', true)->count(),
            'active_courses' => Subject::where('is_active', true)->count(),
            'active_sections' => CourseSection::where('status', 'open')->when($term, fn ($q) => $q->where('term_id', $term->id))->count(),
            'collected' => (clone $payments)->where('status', 'paid')->sum('amount_paid'),
            'outstanding' => max(0, FeePayment::with('feeStructure')->get()->sum(fn ($payment) => $payment->total_due) - FeePayment::where('status', 'paid')->sum('amount_paid')),
            'attendance_rate' => $attendanceTotal ? round(($attendancePresent / $attendanceTotal) * 100, 1) : 0,
            'average_mark' => round((float) ((clone $marks)->avg('marks_obtained') ?? 0), 1),
            'at_risk' => (clone $marks)->where('grade', 'F')->distinct('student_id')->count('student_id'),
            'graduated' => GraduationRecord::where('status', 'graduated')->count(),
            'pending_approvals' => ExamResultsApproval::where('status', 'pending')->count(),
            'advising_active' => AdvisorAssignment::where('is_active', true)->count(),
            'employees' => Employee::count(),
            'pending_leave' => LeaveRequest::where('status', 'pending')->count(),
            'scholarships' => Scholarship::where('is_active', true)->sum('amount'),
        ];
        $enrollmentCounts = DB::table('enrollments')
            ->join('course_sections', 'course_sections.id', '=', 'enrollments.course_section_id')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->join('programs', 'programs.id', '=', 'students.program_id')
            ->whereIn('enrollments.status', Enrollment::ACTIVE_STATUSES)
            ->when($term, fn ($q) => $q->where('course_sections.term_id', $term->id));
        $facultyCounts = (clone $enrollmentCounts)->join('departments', 'departments.id', '=', 'programs.department_id')->select('departments.faculty_id', DB::raw('count(*) as total'))->groupBy('departments.faculty_id')->pluck('total', 'faculty_id');
        $departmentCounts = (clone $enrollmentCounts)->select('programs.department_id', DB::raw('count(*) as total'))->groupBy('programs.department_id')->pluck('total', 'department_id');
        $programCounts = (clone $enrollmentCounts)->select('students.program_id', DB::raw('count(*) as total'))->groupBy('students.program_id')->pluck('total', 'program_id');
        $facultyEnrollment = Faculty::where('is_active', true)->get()->map(fn ($faculty) => tap($faculty, fn ($item) => $item->enrollment_count = (int) ($facultyCounts[$item->id] ?? 0)));
        $departmentEnrollment = Department::where('is_active', true)->get()->map(fn ($department) => tap($department, fn ($item) => $item->enrollment_count = (int) ($departmentCounts[$item->id] ?? 0)));
        $programEnrollment = Program::where('is_active', true)->get()->map(fn ($program) => tap($program, fn ($item) => $item->enrollment_count = (int) ($programCounts[$item->id] ?? 0)));
        $admissions = [
            'pending' => (clone $applications)->whereIn('status', ['pending', 'submitted', 'under_review'])->count(),
            'approved' => $approvedApplications,
            'declined' => (clone $applications)->where('status', 'declined')->count(),
        ];
        $recentActivity = \App\Models\LiveClassActivity::with('actor')->latest()->take(8)->get();
        $years = AcademicYear::orderByDesc('starts_on')->get();
        $terms = $year?->terms()->orderBy('starts_on')->get() ?? collect();

        return view('dashboard.executive', compact('user', 'year', 'term', 'from', 'to', 'stats', 'admissions', 'facultyEnrollment', 'departmentEnrollment', 'programEnrollment', 'recentActivity', 'years', 'terms'));
    }

    protected function deanDashboard(User $user)
    {
        abort_unless($user->faculty_id, 403, 'A faculty assignment is required for Dean access.');

        $faculty = Faculty::whereKey($user->faculty_id)->where('is_active', true)->firstOrFail();
        $year = AcademicYear::find(request()->integer('academic_year_id'))
            ?? AcademicYear::where('is_current', true)->first();
        if (request()->filled('academic_year_id') && !$year) {
            abort(404);
        }
        $term = request()->filled('term_id')
            ? $year?->terms()->find(request()->integer('term_id'))
            : $year?->terms()->where('is_current', true)->first();
        if (request()->filled('term_id') && !$term) {
            abort(404);
        }

        $department = Department::where('faculty_id', $faculty->id)
            ->where('is_active', true)
            ->find(request()->integer('department_id'));
        if (request()->filled('department_id') && !$department) {
            abort(404);
        }
        $program = Program::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))
            ->where('is_active', true)
            ->find(request()->integer('program_id'));
        if (request()->filled('program_id') && !$program) {
            abort(404);
        }
        if ($department && $program && $program->department_id !== $department->id) {
            abort(404);
        }

        $from = $term?->starts_on?->startOfDay() ?? $year?->starts_on?->startOfDay() ?? now()->startOfYear();
        $to = $term?->ends_on?->endOfDay() ?? $year?->ends_on?->endOfDay() ?? now()->endOfYear();
        $studentScope = Student::query()
            ->where('students.status', 'active')
            ->whereHas('program.department', fn ($query) => $query->where('departments.faculty_id', $faculty->id))
            ->when($department, fn ($query) => $query->whereHas('program', fn ($programQuery) => $programQuery->where('department_id', $department->id)))
            ->when($program, fn ($query) => $query->where('program_id', $program->id));
        $sectionScope = CourseSection::query()
            ->whereHas('course.department', fn ($query) => $query->where('departments.faculty_id', $faculty->id))
            ->when($term, fn ($query) => $query->where('term_id', $term->id))
            ->when($department, fn ($query) => $query->whereHas('course', fn ($course) => $course->where('department_id', $department->id)))
            ->when($program, fn ($query) => $query->whereHas('course.curricula', fn ($curriculum) => $curriculum->where('program_id', $program->id)));
        $enrollmentScope = Enrollment::query()
            ->whereIn('enrollments.status', Enrollment::ACTIVE_STATUSES)
            ->whereHas('student', fn ($query) => $query
                ->whereHas('program.department', fn ($departmentQuery) => $departmentQuery->where('faculty_id', $faculty->id))
                ->when($department, fn ($q) => $q->whereHas('program', fn ($p) => $p->where('department_id', $department->id)))
                ->when($program, fn ($q) => $q->where('program_id', $program->id)))
            ->when($term, fn ($query) => $query->whereHas('courseSection', fn ($section) => $section->where('term_id', $term->id)));

        $attendance = Attendance::query()->whereHas('student', fn ($query) => $query
            ->whereHas('program.department', fn ($departmentQuery) => $departmentQuery->where('faculty_id', $faculty->id))
            ->when($department, fn ($q) => $q->whereHas('program', fn ($p) => $p->where('department_id', $department->id)))
            ->when($program, fn ($q) => $q->where('program_id', $program->id)))
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]);
        $marks = ExamMark::query()->whereHas('student', fn ($query) => $query
            ->whereHas('program.department', fn ($departmentQuery) => $departmentQuery->where('faculty_id', $faculty->id))
            ->when($department, fn ($q) => $q->whereHas('program', fn ($p) => $p->where('department_id', $department->id)))
            ->when($program, fn ($q) => $q->where('program_id', $program->id)))
            ->whereBetween('exam_marks.created_at', [$from, $to])
            ->whereNotNull('marks_obtained');

        $attendanceTotal = (clone $attendance)->count();
        $attendancePresent = (clone $attendance)->whereIn('status', ['present', 'late'])->count();
        $marksTotal = (clone $marks)->count();
        $passedMarks = (clone $marks)->where('grade', '!=', 'F')->count();
        $stats = [
            'students' => (clone $studentScope)->count(),
            'active_enrollments' => (clone $enrollmentScope)->count(),
            'departments' => Department::where('faculty_id', $faculty->id)->where('is_active', true)->count(),
            'programs' => Program::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))->where('is_active', true)->count(),
            'courses' => Subject::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))->where('is_active', true)->count(),
            'sections' => (clone $sectionScope)->where('status', 'open')->count(),
            'teaching_staff' => (clone $sectionScope)->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
            'attendance_rate' => $attendanceTotal ? round(($attendancePresent / $attendanceTotal) * 100, 1) : 0,
            'average_mark' => round((float) ((clone $marks)->avg('marks_obtained') ?? 0), 1),
            'pass_rate' => $marksTotal ? round(($passedMarks / $marksTotal) * 100, 1) : 0,
            'at_risk' => (clone $marks)->where('grade', 'F')->distinct('student_id')->count('student_id'),
            'probation' => AcademicStanding::whereIn('status', ['probation', 'suspended', 'dismissed'])->whereHas('student', fn ($query) => $query->whereIn('id', (clone $studentScope)->select('students.id')))->count(),
            'graduated' => GraduationRecord::where('status', 'graduated')->whereHas('student', fn ($query) => $query->whereIn('id', (clone $studentScope)->select('students.id')))->count(),
            'advising_active' => AdvisorAssignment::where('is_active', true)->whereIn('student_id', (clone $studentScope)->select('students.id'))->count(),
            'advising_appointments' => AdvisingAppointment::whereIn('status', ['requested', 'scheduled'])->whereIn('student_id', (clone $studentScope)->select('students.id'))->count(),
        ];
        $enrollmentCounts = DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->join('programs', 'programs.id', '=', 'students.program_id')
            ->join('departments', 'departments.id', '=', 'programs.department_id')
            ->whereIn('enrollments.status', Enrollment::ACTIVE_STATUSES)
            ->where('students.status', 'active')
            ->where('programs.is_active', true)
            ->where('departments.is_active', true)
            ->where('departments.faculty_id', $faculty->id)
            ->when($term, fn ($query) => $query->whereExists(function ($subquery) use ($term): void {
                $subquery->selectRaw('1')
                    ->from('course_sections')
                    ->whereColumn('course_sections.id', 'enrollments.course_section_id')
                    ->where('course_sections.term_id', $term->id);
            }))
            ->when($department, fn ($query) => $query->where('departments.id', $department->id))
            ->when($program, fn ($query) => $query->where('programs.id', $program->id))
            ->select('programs.id as program_id', 'departments.id as department_id', DB::raw('count(*) as total'))
            ->groupBy('programs.id', 'departments.id')
            ->get();
        $programCounts = $enrollmentCounts->pluck('total', 'program_id');
        $departmentCounts = $enrollmentCounts->groupBy('department_id')->map(fn ($rows) => $rows->sum('total'));
        $programEnrollment = Program::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))
            ->where('is_active', true)
            ->when($department, fn ($query) => $query->where('department_id', $department->id))
            ->when($program, fn ($query) => $query->whereKey($program->id))
            ->get()
            ->map(function (Program $item) use ($programCounts): Program {
                $item->student_count = (int) ($programCounts[$item->id] ?? 0);
                return $item;
            })
            ->sortByDesc('student_count')
            ->values();
        $departmentEnrollment = Department::where('faculty_id', $faculty->id)->where('is_active', true)
            ->when($department, fn ($query) => $query->whereKey($department->id))
            ->get()
            ->map(function (Department $item) use ($departmentCounts): Department {
                $item->student_count = (int) ($departmentCounts[$item->id] ?? 0);
                return $item;
            });
        $standing = AcademicStanding::whereIn('status', ['good', 'probation', 'suspended', 'dismissed'])
            ->whereHas('student', fn ($query) => $query->whereIn('id', (clone $studentScope)->select('students.id')))
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $teacherOverview = (clone $sectionScope)->with('teacher')->withCount('enrollments')->whereNotNull('teacher_id')->get()
            ->groupBy('teacher_id')->map(fn ($sections) => ['teacher' => $sections->first()->teacher, 'sections' => $sections->count(), 'enrollments' => $sections->sum('enrollments_count')])->values();
        $departmentOverview = Department::where('faculty_id', $faculty->id)
            ->where('is_active', true)
            ->when($department, fn ($query) => $query->whereKey($department->id))
            ->with('head')
            ->withCount(['programs', 'subjects'])
            ->get();
        $courseOverview = Subject::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))
            ->where('is_active', true)
            ->when($department, fn ($query) => $query->where('department_id', $department->id))
            ->with('department')
            ->withCount('courseSections')
            ->orderBy('name')
            ->take(12)
            ->get();
        $sectionOverview = (clone $sectionScope)
            ->with(['course.department', 'term', 'teacher'])
            ->withCount('enrollments')
            ->latest()
            ->take(12)
            ->get();
        $studentOverview = (clone $studentScope)
            ->with(['user', 'program.department'])
            ->latest()
            ->take(12)
            ->get();
        $timetable = Timetable::with(['subject', 'teacher.user'])->where('is_active', true)
            ->whereHas('subject.department', fn ($query) => $query->where('faculty_id', $faculty->id))
            ->latest()->take(8)->get();
        $years = AcademicYear::orderByDesc('starts_on')->get();
        $terms = $year?->terms()->orderBy('starts_on')->get() ?? collect();
        $departments = Department::where('faculty_id', $faculty->id)->where('is_active', true)->orderBy('name')->get();
        $programs = Program::whereHas('department', fn ($query) => $query->where('faculty_id', $faculty->id))->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.dean', compact('user', 'faculty', 'year', 'term', 'from', 'to', 'department', 'program', 'stats', 'standing', 'programEnrollment', 'departmentEnrollment', 'teacherOverview', 'departmentOverview', 'courseOverview', 'sectionOverview', 'studentOverview', 'timetable', 'years', 'terms', 'departments', 'programs'));
    }

    protected function departmentHeadDashboard(User $user)
    {
        abort_unless($user->isDepartmentHead(), 403);

        $department = Department::where('head_user_id', $user->id)->where('is_active', true)->first();
        abort_unless($department, 403, 'A department assignment is required for Department Head access.');

        $year = AcademicYear::find(request()->integer('academic_year_id'))
            ?? AcademicYear::where('is_current', true)->first();
        if (request()->filled('academic_year_id') && !$year) {
            abort(404);
        }
        $term = $year?->terms()->find(request()->integer('term_id'))
            ?? $year?->terms()->where('is_current', true)->first();
        if (request()->filled('term_id') && !$term) {
            abort(404);
        }
        $program = Program::where('department_id', $department->id)
            ->where('is_active', true)
            ->find(request()->integer('program_id'));
        if (request()->filled('program_id') && !$program) {
            abort(404);
        }

        $from = $term?->starts_on?->startOfDay() ?? $year?->starts_on?->startOfDay() ?? now()->startOfYear();
        $to = $term?->ends_on?->endOfDay() ?? $year?->ends_on?->endOfDay() ?? now()->endOfYear();
        $studentScope = Student::query()
            ->where('students.status', 'active')
            ->whereHas('program', fn ($query) => $query->where('department_id', $department->id))
            ->when($program, fn ($query) => $query->where('program_id', $program->id));
        $sectionScope = CourseSection::query()
            ->whereHas('course', fn ($query) => $query->where('department_id', $department->id))
            ->when($term, fn ($query) => $query->where('term_id', $term->id))
            ->when($program, fn ($query) => $query->whereHas('course.curricula', fn ($query) => $query->where('program_id', $program->id)));
        $enrollmentScope = Enrollment::query()
            ->whereIn('enrollments.status', Enrollment::ACTIVE_STATUSES)
            ->whereHas('student', fn ($query) => $query
                ->whereHas('program', fn ($programQuery) => $programQuery->where('department_id', $department->id))
                ->when($program, fn ($q) => $q->where('program_id', $program->id)))
            ->when($term, fn ($query) => $query->whereHas('courseSection', fn ($section) => $section->where('term_id', $term->id)));
        $attendance = Attendance::query()
            ->whereIn('student_id', (clone $studentScope)->select('students.id'))
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]);
        $marks = ExamMark::query()
            ->whereIn('student_id', (clone $studentScope)->select('students.id'))
            ->whereHas('exam.subject', fn ($query) => $query->where('department_id', $department->id))
            ->whereBetween('exam_marks.created_at', [$from, $to])
            ->whereNotNull('marks_obtained');

        $attendanceTotal = (clone $attendance)->count();
        $marksTotal = (clone $marks)->count();
        $stats = [
            'students' => (clone $studentScope)->count(),
            'active_enrollments' => (clone $enrollmentScope)->count(),
            'programs' => Program::where('department_id', $department->id)->where('is_active', true)->count(),
            'courses' => Subject::where('department_id', $department->id)->where('is_active', true)->count(),
            'sections' => (clone $sectionScope)->where('status', 'open')->count(),
            'teachers' => (clone $sectionScope)->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
            'attendance_rate' => $attendanceTotal ? round(((clone $attendance)->whereIn('status', ['present', 'late'])->count() / $attendanceTotal) * 100, 1) : 0,
            'average_mark' => round((float) ((clone $marks)->avg('marks_obtained') ?? 0), 1),
            'pass_rate' => $marksTotal ? round(((clone $marks)->where('grade', '!=', 'F')->count() / $marksTotal) * 100, 1) : 0,
            'at_risk' => (clone $marks)->where('grade', 'F')->distinct('student_id')->count('student_id'),
            'probation' => AcademicStanding::whereIn('status', ['probation', 'suspended', 'dismissed'])
                ->whereIn('student_id', (clone $studentScope)->select('students.id'))->count(),
            'advising_active' => AdvisorAssignment::where('is_active', true)->whereIn('student_id', (clone $studentScope)->select('students.id'))->count(),
            'advising_appointments' => AdvisingAppointment::whereIn('status', ['requested', 'scheduled'])->whereIn('student_id', (clone $studentScope)->select('students.id'))->count(),
        ];
        $programCounts = (clone $enrollmentScope)
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->select('students.program_id', DB::raw('count(*) as total'))
            ->groupBy('students.program_id')->pluck('total', 'program_id');
        $programEnrollment = Program::where('department_id', $department->id)->where('is_active', true)
            ->when($program, fn ($query) => $query->whereKey($program->id))
            ->get()->map(function (Program $item) use ($programCounts): Program {
                $item->student_count = (int) ($programCounts[$item->id] ?? 0);
                return $item;
            })->sortByDesc('student_count')->values();
        $standing = AcademicStanding::whereIn('status', ['good', 'probation', 'suspended', 'dismissed'])
            ->whereIn('student_id', (clone $studentScope)->select('students.id'))
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $teacherOverview = (clone $sectionScope)->with('teacher')->withCount('enrollments')
            ->whereNotNull('teacher_id')->get()->groupBy('teacher_id')
            ->map(fn ($sections) => ['teacher' => $sections->first()->teacher, 'sections' => $sections->count(), 'enrollments' => $sections->sum('enrollments_count')])
            ->values();
        $coursePerformance = (clone $marks)->with('exam.subject')->get()->groupBy(fn ($mark) => $mark->exam?->subject_id)
            ->map(fn ($courseMarks) => ['course' => $courseMarks->first()->exam?->subject, 'average' => round((float) $courseMarks->avg('marks_obtained'), 1), 'pass_rate' => round(($courseMarks->where('grade', '!=', 'F')->count() / max(1, $courseMarks->count())) * 100, 1)])
            ->filter(fn ($item) => $item['course'])->values();
        $timetable = Timetable::with(['subject', 'teacher.user'])->where('is_active', true)
            ->whereHas('subject', fn ($query) => $query->where('department_id', $department->id))
            ->latest()->take(8)->get();
        $years = AcademicYear::orderByDesc('starts_on')->get();
        $terms = $year?->terms()->orderBy('starts_on')->get() ?? collect();

        return view('dashboard.department-head', compact('user', 'department', 'year', 'term', 'program', 'stats', 'standing', 'programEnrollment', 'teacherOverview', 'coursePerformance', 'timetable', 'years', 'terms'));
    }

    protected function admissionsOfficerDashboard($user)
    {
        $totalApplications = AdmissionApplication::count();
        $newApplications = AdmissionApplication::whereDate('created_at', today())->count();
        $pendingApplications = AdmissionApplication::whereIn('status', ['submitted', 'under_review'])->count();
        $approvedApplications = AdmissionApplication::where('status', 'approved')->count();
        $rejectedApplications = AdmissionApplication::where('status', 'declined')->count();
        $documentsPending = AdmissionApplication::query()
            ->whereNotNull('documents')
            ->get(['documents'])
            ->sum(fn ($application) => collect($application->documents ?? [])->where('status', 'submitted')->count());

        $statuses = [
            'submitted' => AdmissionApplication::where('status', 'submitted')->count(),
            'under_review' => AdmissionApplication::where('status', 'under_review')->count(),
            'approved' => $approvedApplications,
            'declined' => $rejectedApplications,
        ];

        $recentApplications = AdmissionApplication::latest()->take(10)->get();

        $myWork = [
            'needing_review' => AdmissionApplication::where('status', 'submitted')->latest()->take(5)->get(),
            'documents_pending' => AdmissionApplication::query()
                ->whereNotNull('documents')
                ->get()
                ->filter(fn ($application) => collect($application->documents ?? [])->contains('status', 'submitted'))
                ->sortByDesc('created_at')
                ->take(5),
            'decisions_pending' => AdmissionApplication::where('status', 'under_review')->latest()->take(5)->get(),
        ];

        $applicantsByGrade = AdmissionApplication::select('grade_interested', DB::raw('count(*) as count'))
            ->groupBy('grade_interested')
            ->get();

        return view('dashboard.admissions-officer', compact(
            'user', 'totalApplications', 'newApplications', 'pendingApplications', 'documentsPending',
            'approvedApplications', 'rejectedApplications', 'statuses', 'recentApplications', 'applicantsByGrade', 'myWork'
        ));
    }

    protected function financeOfficerDashboard($user)
    {
        $request = request();
        $invoiceQuery = Invoice::query()
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->integer('academic_year_id')))
            ->when($request->filled('term_id'), fn ($query) => $query->where('term_id', $request->integer('term_id')));
        $paymentQuery = FeePayment::query();
        $totalStudents = Student::count();
        $totalFeesCollected = (clone $paymentQuery)->whereIn('status', ['paid', 'partial'])->sum('amount_paid');
        $pendingPaymentCount = FeePayment::where('status', 'pending')->count();
        $overduePaymentCount = FeePayment::where('status', 'overdue')->count();

        $stats = [
            'total_students' => $totalStudents,
            'total_collected' => $totalFeesCollected,
            'total_pending' => $pendingPaymentCount,
            'overdue_count' => $overduePaymentCount,
            'collected_percentage' => 0,
            'total_billed' => (clone $invoiceQuery)->sum('total'),
            'outstanding_balance' => (clone $invoiceQuery)->sum('balance'),
            'refunds' => Refund::whereIn('status', ['approved', 'processed'])->sum('amount'),
            'reconciliation_exceptions' => (clone $paymentQuery)->whereNull('reconciled_at')->whereIn('status', ['paid', 'partial'])->count(),
        ];

        $paymentsByStatus = [
            'paid' => (clone $paymentQuery)->where('status', 'paid')->count(),
            'partial' => (clone $paymentQuery)->where('status', 'partial')->count(),
            'pending' => (clone $paymentQuery)->where('status', 'pending')->count(),
            'overdue' => (clone $paymentQuery)->where('status', 'overdue')->count(),
        ];

        $recentPayments = FeePayment::with(['student.user', 'feeStructure'])
            ->latest()
            ->take(10)
            ->get();

        $outstandingBalances = FeePayment::with(['student.user', 'feeStructure'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->latest()
            ->take(10)
            ->get();

        $feeByClass = SchoolClass::with(['feeStructures.payments' => fn($q) => $q->where('status', '!=', 'pending')])
            ->get()
            ->map(function ($class) {
                return [
                    'name' => $class->name,
                    'collected' => (float)$class->feeStructures->flatMap->payments->sum('amount_paid'),
                    'student_count' => $class->students->count(),
                ];
            });

        return view('dashboard.finance-officer', compact(
            'user', 'stats', 'paymentsByStatus', 'recentPayments', 'outstandingBalances', 'feeByClass'
        ));
    }

    protected function registrarDashboard($user)
    {
        $totalStudents = Student::count();
        $transcriptsGenerated = \App\Models\Transcript::count();
        $graduationEligible = \App\Models\GraduationRecord::where('status', 'eligible')->count();
        $graduated = \App\Models\GraduationRecord::where('status', 'graduated')->count();

        $stats = [
            'total_students' => $totalStudents,
            'transcripts_generated' => $transcriptsGenerated,
            'graduation_eligible' => $graduationEligible,
            'graduated' => $graduated,
        ];

        $recentTranscripts = \App\Models\Transcript::with('student.user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.registrar', compact('user', 'stats', 'recentTranscripts'));
    }

    protected function hrOfficerDashboard($user)
    {
        $totalEmployees = \App\Models\Employee::count();
        $pendingLeaveRequests = \App\Models\LeaveRequest::where('status', 'pending')->count();
        $approvedLeaveRequests = \App\Models\LeaveRequest::where('status', 'approved')->count();
        $totalLeaveTypes = \App\Models\LeaveType::count();

        $stats = [
            'total_employees' => $totalEmployees,
            'pending_leave_requests' => $pendingLeaveRequests,
            'approved_leave_requests' => $approvedLeaveRequests,
            'leave_types' => $totalLeaveTypes,
        ];

        $recentLeaveRequests = \App\Models\LeaveRequest::with(['employee.user', 'leaveType'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.hr-officer', compact('user', 'stats', 'recentLeaveRequests'));
    }

    protected function librarianDashboard($user)
    {
        $totalBooks = \App\Models\Book::sum('total_copies');
        $availableBooks = \App\Models\Book::sum('available_copies');
        $totalMembers = \App\Models\LibraryMember::count();
        $activeBorrowings = \App\Models\Borrowing::where('status', 'active')->count();
        $overdueBooks = \App\Models\Borrowing::where('status', 'overdue')->count();

        $stats = [
            'total_books' => $totalBooks,
            'available_books' => $availableBooks,
            'total_members' => $totalMembers,
            'active_borrowings' => $activeBorrowings,
            'overdue_books' => $overdueBooks,
        ];

        $recentBorrowings = \App\Models\Borrowing::with(['libraryMember.user', 'book'])
            ->latest()
            ->take(10)
            ->get();

        $reservations = \App\Models\BookReservation::with(['libraryMember.user', 'book'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.librarian', compact('user', 'stats', 'recentBorrowings', 'reservations'));
    }

    protected function staffDashboard($user)
    {
        $studentStats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'attention' => Student::whereIn('status', ['inactive', 'graduated', 'expelled'])->count(),
        ];

        $parentCount = Guardian::count();
        $pendingApplications = AdmissionApplication::whereIn('status', ['pending', 'submitted', 'under_review'])->count();

        $recentStudents = Student::with(['user', 'schoolClass', 'section'])
            ->latest()
            ->take(5)
            ->get();

        $recentParents = Guardian::with(['user', 'students.user'])
            ->latest()
            ->take(5)
            ->get();

        $notices = Notice::where('is_active', true)
            ->where('status', 'published')
            ->whereDate('published_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            })
            ->latest('published_date')
            ->take(5)
            ->get();

        return view('dashboard.staff', compact(
            'user', 'studentStats', 'parentCount', 'pendingApplications',
            'recentStudents', 'recentParents', 'notices'
        ));
    }

    public function parentChildrenPage()
    {
        $user = Auth::user();

        abort_unless($user && $user->isParent(), 403, 'Parent access required.');

        $guardian = $user->guardian()->with(['students.user', 'students.schoolClass', 'students.section'])->first();
        $children = $guardian ? $guardian->students()->with(['user', 'schoolClass', 'section'])->get() : collect();

        $requestedStudentId = request('student_id');
        if ($requestedStudentId !== null && ! $children->contains(fn ($child) => (int) $child->id === (int) $requestedStudentId)) {
            abort(403, 'Unauthorized access to child record.');
        }

        return view('parent.children', compact('children'));
    }

    public function parentChildPage(Student $student)
    {
        $user = Auth::user();

        abort_unless($user && $user->isParent(), 403, 'Parent access required.');

        $guardian = $user->guardian()->with(['students.user', 'students.schoolClass', 'students.section'])->first();
        $children = $guardian ? $guardian->students()->with(['user', 'schoolClass', 'section'])->get() : collect();

        abort_unless($guardian && $children->contains(fn ($child) => (int) $child->id === (int) $student->id), 403, 'Unauthorized access to child record.');

        $selectedChild = $children->firstWhere('id', $student->id) ?? $student->load(['user', 'schoolClass', 'section']);

        $attendances = $selectedChild->attendances ?? collect();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays  = $attendances->where('status', 'absent')->count();
        $lateDays    = $attendances->where('status', 'late')->count();
        $totalDays   = $attendances->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100;

        $examMarks = ExamMark::where('student_id', $selectedChild->id)->with(['exam.subject'])->latest()->get();
        $totalMarksObtained = $examMarks->where('is_absent', false)->sum('marks_obtained');
        $totalMaxMarks = $examMarks->sum(fn($m) => $m->exam->total_marks ?? 100);
        $overallGpaPct = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 1) : 0;
        $overallGrade = ExamMark::computeGrade($totalMarksObtained, $totalMaxMarks ?: 1);

        $upcomingExams = Exam::where('school_class_id', $selectedChild->school_class_id)
            ->where('status', '!=', 'cancelled')
            ->latest('exam_date')
            ->take(5)
            ->get();

        $feePayments = $selectedChild->feePayments ?? collect();
        $totalFeePaid = $feePayments->where('status', 'paid')->sum('amount_paid');
        $pendingFeeCount = $feePayments->where('status', 'pending')->count();

        $notices = Notice::where('is_active', true)->latest()->take(5)->get();

        return view('parent.child', compact(
            'children', 'selectedChild', 'presentDays', 'absentDays', 'lateDays', 'totalDays',
            'attendanceRate', 'examMarks', 'overallGpaPct', 'overallGrade', 'upcomingExams',
            'feePayments', 'totalFeePaid', 'pendingFeeCount', 'notices'
        ));
    }

    public function studentDashboardPage()
    {
        $user = Auth::user();

        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        return $this->studentDashboard($user);
    }

    public function studentLiveClassesPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        $liveClasses = $student
            ? LiveClass::with(['teacher', 'subject', 'schoolClass'])
                ->where('school_class_id', $student->school_class_id)
                ->whereIn('status', ['scheduled', 'live', 'ended'])
                ->orderByRaw("CASE status WHEN 'live' THEN 0 WHEN 'scheduled' THEN 1 ELSE 2 END")
                ->orderByDesc('scheduled_at')
                ->get()
            : collect();

        return view('student.live-classes', compact('student', 'liveClasses'));
    }

    public function studentLiveClassesStatus()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        $statuses = $student
            ? LiveClass::where('school_class_id', $student->school_class_id)
                ->whereIn('status', ['scheduled', 'live', 'ended'])
                ->pluck('status', 'id')
            : collect();

        return response()->json($statuses);
    }

    public function studentCoursesPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass.subjects', 'schoolClass', 'enrollments' => fn ($query) => $query->whereIn('status', \App\Models\Enrollment::ACTIVE_STATUSES)->with('courseSection.course', 'courseSection.term')])->where('user_id', $user->id)->first();
        $subjects = $student?->schoolClass?->subjects ?? collect();
        $enrollments = $student?->enrollments ?? collect();

        return view('student.courses', compact('student', 'subjects', 'enrollments'));
    }

    public function studentVideosPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass'])->where('user_id', $user->id)->first();
        $videos = $student
            ? LectureVideo::with(['teacher.user', 'subject', 'schoolClass'])
                ->where('school_class_id', $student->school_class_id)
                ->latest()
                ->get()
            : collect();

        return view('student.videos', compact('student', 'videos'));
    }

    public function studentMaterialsPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass'])->where('user_id', $user->id)->first();
        $materials = $student
            ? CourseMaterial::with(['teacher.user', 'subject', 'schoolClass'])
                ->where('school_class_id', $student->school_class_id)
                ->latest()
                ->get()
            : collect();

        return view('student.materials', compact('student', 'materials'));
    }

    public function studentAssignmentsPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass'])->where('user_id', $user->id)->first();
        $assignments = $student
            ? Assignment::with(['teacher.user', 'subject', 'schoolClass', 'rubric.criteria', 'submissions' => fn ($query) => $query->with(['grader', 'rubricScores'])->where('student_id', $student->id)->latest('attempt_number')])
                ->where('school_class_id', $student->school_class_id)
                ->where('status', 'published')
                ->where(function ($query): void {
                    $query->whereNull('available_from')->orWhere('available_from', '<=', now());
                })
                ->latest('due_date')
                ->get()
            : collect();

        return view('student.assignments', compact('student', 'assignments'));
    }

    public function studentAttendancePage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass', 'section'])->where('user_id', $user->id)->first();
        $attendances = $student
            ? Attendance::with(['schoolClass', 'section'])
                ->where('student_id', $student->id)
                ->latest('attendance_date')
                ->get()
            : collect();

        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $totalDays = $attendances->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        return view('student.attendance', compact('student', 'attendances', 'attendanceRate', 'presentDays', 'absentDays', 'lateDays', 'totalDays'));
    }

    public function studentAnnouncementsPage()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');

        $student = Student::with(['schoolClass'])->where('user_id', $user->id)->first();
        $announcements = Notice::where('is_active', true)->latest('published_date')->get();

        return view('student.announcements', compact('student', 'announcements'));
    }

    /**
     * Admin Executive Dashboard
     */
    protected function adminDashboard($user)
    {
        $totalStudents = Student::count();
        $presentToday  = Attendance::where('attendance_date', today())->where('status', 'present')->count();
        $absentToday   = Attendance::where('attendance_date', today())->where('status', 'absent')->count();

        $stats = [
            'total_students'  => $totalStudents,
            'total_teachers'  => Teacher::count(),
            'total_classes'   => SchoolClass::count(),
            'fees_collected'  => FeePayment::where('status', 'paid')->sum('amount_paid'),
            'fees_pending'    => FeePayment::where('status', 'pending')->count(),
            'present_today'   => $presentToday,
            'absent_today'    => $absentToday,
            'active_students' => Student::where('status', 'active')->count(),
            'total_exams'     => Exam::count(),
            'scheduled_exams' => Exam::where('status', 'scheduled')->count(),
            'active_notices'  => Notice::where('is_active', true)->count(),
            'new_inquiries'   => Inquiry::count(),
            'total_tests'     => \App\Models\Test::count(),
            'published_tests' => \App\Models\Test::where('status', 'published')->count(),
            'test_submissions' => \App\Models\TestAttempt::where('status', '!=', 'in_progress')->count(),
            'pending_applications' => AdmissionApplication::whereIn('status', ['pending', 'submitted', 'under_review'])->count(),
            'approved_applications' => AdmissionApplication::where('status', 'approved')->count(),
            'attendance_rate' => ($presentToday + $absentToday > 0)
                ? round(($presentToday / ($presentToday + $absentToday)) * 100, 1)
                : 100,
        ];

        // Recent students
        $recentStudents = Student::with(['user', 'schoolClass', 'section'])
            ->latest()
            ->take(5)
            ->get();

        // Attendance chart data (last 7 days)
        $attendanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $pCount = Attendance::where('attendance_date', $date)->where('status', 'present')->count();
            $aCount = Attendance::where('attendance_date', $date)->where('status', 'absent')->count();
            
            $attendanceData[] = [
                'date'    => now()->subDays($i)->format('M d'),
                'present' => $pCount,
                'absent'  => $aCount,
            ];
        }

        // Fee collection by class
        $feeByClass = SchoolClass::with(['feeStructures.payments' => fn($q) => $q->where('status', 'paid')])
            ->get()
            ->map(function ($class) {
                return [
                    'name'      => $class->name,
                    'collected' => (float)$class->feeStructures->flatMap->payments->sum('amount_paid'),
                ];
            });

        // Recent fee payments
        $recentPayments = FeePayment::with(['student.user', 'feeStructure'])
            ->where('status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming / Recent Exams
        $upcomingExams = Exam::with(['schoolClass', 'subject'])
            ->latest('exam_date')
            ->take(4)
            ->get();

        // Latest Notices
        $recentNotices = Notice::latest()
            ->take(4)
            ->get();

        $recentVideoNotices = Notice::where(function ($query) {
                $query->whereNotNull('video_path')->orWhereNotNull('external_video_url');
            })
            ->latest('published_date')
            ->take(3)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'recentStudents', 'attendanceData', 'feeByClass', 'recentPayments', 'upcomingExams', 'recentNotices', 'recentVideoNotices'
        ));
    }

    protected function teacherPortalData($user): array
    {
        $teacher = Teacher::with(['classTeacherOf.sections', 'classTeacherOf.students.user'])->where('user_id', $user->id)->first();

        $teacherClassIds = [];
        if ($teacher && $teacher->class_teacher_of) {
            $teacherClassIds[] = $teacher->class_teacher_of;
        }

        if ($teacher) {
            $teacherClassIds = array_values(array_unique(array_merge(
                $teacherClassIds,
                DB::table('class_subject')->where('teacher_id', $teacher->user_id)->pluck('school_class_id')->all()
            )));
        }

        $teacherClasses = ! empty($teacherClassIds)
            ? SchoolClass::with(['students.user', 'subjects'])->whereIn('id', $teacherClassIds)->get()
            : collect();

        $teacherSubjects = $teacherClasses->flatMap(function ($class) {
            return $class->subjects ?? collect();
        })->unique('id')->values();

        $teacherCourseRows = $teacherClasses->flatMap(function ($class) {
            return $class->subjects->map(function ($subject) use ($class) {
                return ['class' => $class, 'subject' => $subject];
            });
        })->values();

        $teacherVideos = $teacher
            ? LectureVideo::with(['schoolClass', 'subject'])->where('teacher_id', $teacher->id)->latest()->get()
            : collect();

        $teacherMaterials = $teacher
            ? CourseMaterial::with(['schoolClass', 'subject'])->where('teacher_id', $teacher->id)->latest()->get()
            : collect();

        $teacherAssignments = $teacher
            ? Assignment::with(['schoolClass', 'subject'])->where('teacher_id', $teacher->id)->latest()->get()
            : collect();

        $assignedClass = $teacherClasses->first();
        $myStudents = $assignedClass
            ? Student::where('school_class_id', $assignedClass->id)->with(['user', 'section'])->get()
            : collect();

        $todayAttendance = $assignedClass
            ? Attendance::where('school_class_id', $assignedClass->id)->where('attendance_date', today())->get()
            : collect();

        $presentToday = $todayAttendance->where('status', 'present')->count();
        $absentToday = $todayAttendance->where('status', 'absent')->count();
        $totalClassStudents = $myStudents->count();
        $isAttendanceMarked = $todayAttendance->isNotEmpty();

        $myExams = $assignedClass
            ? Exam::where('school_class_id', $assignedClass->id)->with(['subject'])->latest('exam_date')->take(6)->get()
            : Exam::with(['schoolClass', 'subject'])->latest('exam_date')->take(6)->get();

        $recentActivity = collect()
            ->merge($teacherVideos->map(fn ($item) => ['created_at' => $item->created_at, 'title' => $item->title, 'type' => 'Video', 'time' => $item->created_at->diffForHumans(), 'meta' => $item->schoolClass->name ?? 'Class']))
            ->merge($teacherMaterials->map(fn ($item) => ['created_at' => $item->created_at, 'title' => $item->title, 'type' => 'PDF', 'time' => $item->created_at->diffForHumans(), 'meta' => $item->schoolClass->name ?? 'Class']))
            ->merge($teacherAssignments->map(fn ($item) => ['created_at' => $item->created_at, 'title' => $item->title, 'type' => 'Assignment', 'time' => $item->created_at->diffForHumans(), 'meta' => $item->due_date?->format('M d, Y') ?? 'Due soon']))
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        $notices = Notice::where('is_active', true)->latest()->take(5)->get();

        $teacherStats = [
            'my_students_count' => $totalClassStudents,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'unmarked_today' => max(0, $totalClassStudents - $presentToday - $absentToday),
            'attendance_rate' => ($presentToday + $absentToday > 0)
                ? round(($presentToday / ($presentToday + $absentToday)) * 100, 1)
                : 100,
            'my_exams_count' => $myExams->count(),
            'active_notices' => $notices->count(),
            'uploaded_videos' => $teacherVideos->count(),
            'uploaded_materials' => $teacherMaterials->count(),
            'assignments_count' => $teacherAssignments->count(),
            'classes_count' => $teacherClasses->count(),
        ];

        $liveClasses = LiveClass::with(['subject', 'schoolClass', 'participants.user'])
            ->where('teacher_id', $user->id)
            ->orderBy('scheduled_at')
            ->get();

        // Get teacher's tests
        $myTests = \App\Models\Test::where('teacher_id', $user->id)
            ->with(['schoolClass', 'subject'])
            ->latest('created_at')
            ->take(6)
            ->get();

        $teacherStats['tests_count'] = $myTests->count();

        return compact(
            'teacher',
            'teacherClassIds',
            'teacherClasses',
            'teacherSubjects',
            'teacherCourseRows',
            'teacherVideos',
            'teacherMaterials',
            'teacherAssignments',
            'assignedClass',
            'myStudents',
            'isAttendanceMarked',
            'todayAttendance',
            'myExams',
            'myTests',
            'notices',
            'teacherStats',
            'recentActivity',
            'liveClasses'
        );
    }

    /**
     * Dedicated Teacher Dashboard
     */
    protected function teacherDashboard($user)
    {
        $data = $this->teacherPortalData($user);

        return view('dashboard.teacher', $data);
    }

    public function teacherClassesPage()
    {
        $user = auth()->user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $this->teacherPortalData($user);

        return view('teacher.classes', $data);
    }

    public function teacherCoursesPage()
    {
        $user = auth()->user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $this->teacherPortalData($user);

        return view('teacher.courses', $data);
    }

    public function teacherVideosPage()
    {
        $user = auth()->user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $this->teacherPortalData($user);

        return view('teacher.videos', $data);
    }

    public function teacherMaterialsPage()
    {
        $user = auth()->user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $this->teacherPortalData($user);

        return view('teacher.materials', $data);
    }

    public function teacherAttendancePage(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $this->teacherPortalData($user);

        $classId = $request->get('class_id', $data['teacherClasses']->first()?->id);
        $date = $request->get('date', now()->toDateString());
        $selectedClass = $data['teacherClasses']->firstWhere('id', $classId) ?? $data['teacherClasses']->first();
        $sectionId = $request->get('section_id');

        $students = collect();
        $sections = collect();

        if ($selectedClass) {
            $sections = \App\Models\Section::where('school_class_id', $selectedClass->id)->get();
            $sectionId = $sections->firstWhere('id', $sectionId)?->id ?? $sections->first()?->id;
            $students = Student::with(['user', 'attendances' => fn ($query) => $query->where('attendance_date', $date)])
                ->where('school_class_id', $selectedClass->id)
                ->where('section_id', $sectionId)
                ->where('status', 'active')
                ->get();
        }

        $attendanceSummaries = Attendance::whereIn('school_class_id', $data['teacherClassIds'])
            ->where('attendance_date', $date)
            ->get()
            ->groupBy('student_id');

        return view('teacher.attendance', array_merge($data, compact(
            'classId',
            'date',
            'selectedClass',
            'sectionId',
            'students',
            'sections',
            'attendanceSummaries'
        )));
    }

    /**
     * Dedicated Student Portal Dashboard
     */
    protected function studentDashboard($user)
    {
        $student = Student::with(['schoolClass.subjects', 'section', 'attendances', 'feePayments.feeStructure'])
            ->where('user_id', $user->id)
            ->first();

        // Auto-provision student profile if not yet created
        if (!$student) {
            $class = SchoolClass::where('is_active', true)->first();
            $section = Section::first();
            if ($class && $section) {
                $student = Student::create([
                    'user_id'          => $user->id,
                    'roll_number'      => 'STU-' . strtoupper(Str::random(5)),
                    'admission_number' => 'ADM-' . date('Y') . '-' . rand(1000, 9999),
                    'school_class_id'  => $class->id,
                    'section_id'       => $section->id,
                    'admission_date'   => today(),
                    'status'           => 'active',
                ]);
                $student->load(['schoolClass.subjects', 'section', 'attendances', 'feePayments.feeStructure']);
            }
        }

        // Attendance stats
        $attendances = $student?->attendances ?? collect();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays  = $attendances->where('status', 'absent')->count();
        $lateDays    = $attendances->where('status', 'late')->count();
        $totalDays   = $attendances->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100;
        $recentAttendance = $attendances->sortByDesc('attendance_date')->take(7);

        // Exam Marks & Results
        $examMarks = $student 
            ? ExamMark::where('student_id', $student->id)->with(['exam.subject'])->latest()->get()
            : collect();

        $totalMarksObtained = $examMarks->where('is_absent', false)->sum('marks_obtained');
        $totalMaxMarks = $examMarks->sum(fn($m) => $m->exam->total_marks ?? 100);
        $overallGpaPct = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 1) : 0;
        $overallGrade = ExamMark::computeGrade($totalMarksObtained, $totalMaxMarks ?: 1);

        // Subject score breakdown for Chart.js
        $subjectScores = $examMarks->map(function ($mark) {
            $max = $mark->exam->total_marks ?: 100;
            return [
                'subject' => $mark->exam->subject->name ?? 'General',
                'pct'     => $mark->is_absent ? 0 : round(($mark->marks_obtained / $max) * 100, 1),
                'grade'   => $mark->grade,
            ];
        });

        // Upcoming exams for student's class
        $upcomingExams = $student 
            ? Exam::where('school_class_id', $student->school_class_id)->where('status', '!=', 'cancelled')->latest('exam_date')->take(5)->get()
            : collect();

        // Enrolled class subjects
        $enrolledSubjects = $student?->schoolClass?->subjects ?? Subject::where('is_active', true)->take(6)->get();

        // Fee Payments & Status
        $feePayments = $student?->feePayments ?? collect();
        $totalFeePaid = $feePayments->where('status', 'paid')->sum('amount_paid');
        $pendingFeeCount = $feePayments->where('status', 'pending')->count();

        // Notices
        $notices = Notice::where('is_active', true)->latest()->take(5)->get();

        $studentVideos = $student
            ? LectureVideo::with(['teacher.user', 'subject'])->where('school_class_id', $student->school_class_id)->latest()->take(5)->get()
            : collect();

        $studentMaterials = $student
            ? CourseMaterial::with(['teacher.user', 'subject'])->where('school_class_id', $student->school_class_id)->latest()->take(5)->get()
            : collect();

        $studentAssignments = $student
            ? Assignment::with(['teacher.user', 'subject'])->where('school_class_id', $student->school_class_id)->latest()->take(5)->get()
            : collect();

        $liveClasses = $student
            ? LiveClass::with(['teacher', 'subject', 'schoolClass', 'participants.user'])
                ->where('school_class_id', $student->school_class_id)
                ->whereIn('status', ['scheduled', 'live'])
                ->orderBy('scheduled_at')
                ->get()
            : collect();

        // Available tests for student's class
        $upcomingTests = $student
            ? \App\Models\Test::where('school_class_id', $student->school_class_id)
                ->where('status', 'published')
                ->with(['subject', 'schoolClass'])
                ->whereDate('publish_date', '>', now())
                ->latest('publish_date')
                ->take(3)
                ->get()
            : collect();

        return view('dashboard.student', compact(
            'student', 'presentDays', 'absentDays', 'lateDays', 'totalDays', 'attendanceRate',
            'recentAttendance', 'examMarks', 'overallGpaPct', 'overallGrade', 'subjectScores',
            'upcomingExams', 'enrolledSubjects', 'feePayments', 'totalFeePaid', 'pendingFeeCount', 'notices',
            'studentVideos', 'studentMaterials', 'studentAssignments', 'liveClasses', 'upcomingTests'
        ));
    }

    /**
     * Dedicated Parent Dashboard
     */
    protected function parentDashboard($user)
    {
        $guardian = \App\Models\Guardian::with([
            'students.user',
            'students.schoolClass.subjects',
            'students.section',
            'students.attendances',
            'students.feePayments.feeStructure',
            'students.examMarks.exam.subject'
        ])->where('user_id', $user->id)->first();

        // If no guardian profile exists yet, create one
        if (!$guardian) {
            $guardian = \App\Models\Guardian::create([
                'user_id'      => $user->id,
                'relationship' => 'parent',
                'occupation'   => 'Parent/Guardian',
            ]);
            $guardian->load(['students.user', 'students.schoolClass', 'students.section']);
        }

        $children = $guardian->students;
        $requestedStudentId = request('student_id');

        if ($requestedStudentId !== null && ! $children->contains(fn ($child) => (int) $child->id === (int) $requestedStudentId)) {
            abort(403, 'Unauthorized access to child record.');
        }

        $selectedStudentId = $requestedStudentId ?: ($children->first()?->id ?? null);
        $selectedChild = $children->firstWhere('id', $selectedStudentId) ?: $children->first();

        // Attendance stats for selected child
        $attendances = $selectedChild?->attendances ?? collect();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays  = $attendances->where('status', 'absent')->count();
        $lateDays    = $attendances->where('status', 'late')->count();
        $totalDays   = $attendances->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100;
        $recentAttendance = $attendances->sortByDesc('attendance_date')->take(7);

        // Marks for selected child
        $examMarks = $selectedChild
            ? ExamMark::where('student_id', $selectedChild->id)->with(['exam.subject'])->latest()->get()
            : collect();

        $totalMarksObtained = $examMarks->where('is_absent', false)->sum('marks_obtained');
        $totalMaxMarks = $examMarks->sum(fn($m) => $m->exam->total_marks ?? 100);
        $overallGpaPct = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 1) : 0;
        $overallGrade = ExamMark::computeGrade($totalMarksObtained, $totalMaxMarks ?: 1);

        // Upcoming exams for child's class
        $upcomingExams = $selectedChild
            ? Exam::where('school_class_id', $selectedChild->school_class_id)->where('status', '!=', 'cancelled')->latest('exam_date')->take(5)->get()
            : collect();

        // Fee payments
        $feePayments = $selectedChild?->feePayments ?? collect();
        $totalFeePaid = $feePayments->where('status', 'paid')->sum('amount_paid');
        $pendingFeeCount = $feePayments->where('status', 'pending')->count();

        // Notices
        $notices = Notice::where('is_active', true)->latest()->take(5)->get();

        // Test attempts and results for selected child
        $testAttempts = $selectedChild
            ? \App\Models\TestAttempt::where('student_id', $selectedChild->id)
                ->with(['test.subject', 'test.schoolClass'])
                ->whereIn('status', ['graded', 'results_released'])
                ->latest('submitted_at')
                ->take(5)
                ->get()
            : collect();

        return view('dashboard.parent', compact(
            'guardian', 'children', 'selectedChild', 'presentDays', 'absentDays', 'lateDays', 'totalDays',
            'attendanceRate', 'recentAttendance', 'examMarks', 'overallGpaPct', 'overallGrade',
            'upcomingExams', 'feePayments', 'totalFeePaid', 'pendingFeeCount', 'notices', 'testAttempts'
        ));
    }

    /**
     * Dashboard Search API
     * Returns role-specific search results
     */
    public function searchDashboard(Request $request)
    {
        $user = Auth::user();
        $query = trim($request->input('q', ''));
        $results = [];

        if (!$user || strlen($query) < 2) {
            return response()->json(['results' => $results]);
        }

        // Teacher: Search their courses, classes, students, live classes
        if ($user->isTeacher()) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                // Search courses
                $results['courses'] = Subject::whereHas('classes', function ($q) use ($teacher) {
                    $q->whereHas('teachers', function ($q2) use ($teacher) {
                        $q2->where('teacher_id', $teacher->id);
                    });
                })->where('name', 'like', "%{$query}%")->limit(5)->get(['id', 'name']);

                // Search enrolled students
                $results['students'] = Student::whereHas('schoolClass.teachers', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->where(function ($q) use ($query) {
                    $q->where('user_id', 'like', "%{$query}%")
                      ->orWhereHas('user', function ($q2) use ($query) {
                          $q2->where('name', 'like', "%{$query}%");
                      });
                })->limit(5)->get(['id', 'user_id'])->load('user');

                // Search live classes
                $results['liveClasses'] = LiveClass::where('teacher_id', $teacher->id)
                    ->where('title', 'like', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'title', 'scheduled_at']);
            }
        }

        // Student: Search their courses, assignments, live classes, announcements
        elseif ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                // Search courses
                $results['courses'] = $student->schoolClass?->subjects()
                    ->where('name', 'like', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'name']) ?? [];

                // Search assignments
                $results['assignments'] = Assignment::where('subject_id', function ($q) use ($student) {
                    $q->selectRaw('id')->from('subjects')
                      ->whereHas('classes', function ($q2) use ($student) {
                          $q2->where('school_class_id', $student->school_class_id);
                      });
                })->where('title', 'like', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'title', 'due_date']);

                // Search live classes  
                $results['liveClasses'] = LiveClass::whereHas('subject', function ($q) use ($student) {
                    $q->whereHas('classes', function ($q2) use ($student) {
                        $q2->where('school_class_id', $student->school_class_id);
                    });
                })->where('title', 'like', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'title', 'scheduled_at']);
            }
        }

        // Parent: Search their children's info
        elseif ($user->isParent()) {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if ($guardian) {
                $results['children'] = $guardian->students()
                    ->where('user_id', function ($q) use ($query) {
                        $q->selectRaw('id')->from('users')
                          ->where('name', 'like', "%{$query}%");
                    })
                    ->orWhere('student_id', 'like', "%{$query}%")
                    ->limit(5)
                    ->get(['id', 'student_id', 'user_id'])->load('user');
            }
        }

        // Admin/Staff/Super Admin: Search students, teachers, classes, subjects
        elseif ($user->isAdmin() || $user->isStaff() || $user->isSuperAdmin()) {
            $results['students'] = Student::where('student_id', 'like', "%{$query}%")
                ->orWhereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'student_id', 'user_id'])->load('user');

            $results['teachers'] = Teacher::where('employee_id', 'like', "%{$query}%")
                ->orWhereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'employee_id', 'user_id'])->load('user');

            $results['classes'] = SchoolClass::where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'name']);

            $results['subjects'] = Subject::where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get(['id', 'name']);
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Calendar Events API
     * Returns role-specific calendar events for the requested month
     */
    public function getCalendarEvents(Request $request)
    {
        $user = Auth::user();
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $events = [];

        if (!$user) {
            return response()->json(['events' => $events]);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->clone()->endOfMonth()->endOfDay();

        // Teacher: Show scheduled classes and live classes
        if ($user->isTeacher()) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                // Timetables (weekly schedule)
                $timetables = Timetable::where('teacher_id', $teacher->id)
                    ->where('is_active', true)
                    ->get();

                // Add timetable events for each day in month
                $currentDate = $startDate->clone();
                while ($currentDate <= $endDate) {
                    $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                    foreach ($timetables as $tt) {
                        if ($tt->day_of_week == $dayOfWeek) {
                            $events[] = [
                                'id' => 'timetable_' . $tt->id . '_' . $currentDate->format('Y-m-d'),
                                'title' => $tt->subject?->name ?? 'Class',
                                'start' => $currentDate->format('Y-m-d') . 'T' . $tt->start_time,
                                'end' => $currentDate->format('Y-m-d') . 'T' . $tt->end_time,
                                'type' => 'class',
                                'color' => '#0d3f38'
                            ];
                        }
                    }
                    $currentDate->addDay();
                }

                // Live classes
                $liveClasses = LiveClass::where('teacher_id', $teacher->id)
                    ->whereBetween('scheduled_at', [$startDate, $endDate])
                    ->get();

                foreach ($liveClasses as $lc) {
                    if ($lc->scheduled_at) {
                        $end = $lc->scheduled_at->clone()->addMinutes($lc->duration_minutes ?? 60);
                        $events[] = [
                            'id' => 'liveclass_' . $lc->id,
                            'title' => $lc->title ?? 'Live Class',
                            'start' => $lc->scheduled_at->format('c'),
                            'end' => $end->format('c'),
                            'type' => 'live',
                            'color' => '#1a5c54'
                        ];
                    }
                }
            }
        }

        // Student: Show their schedule, exams, and live classes
        elseif ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                // Timetables for student's class
                $timetables = Timetable::whereHas('schoolClass', function ($q) use ($student) {
                    $q->where('school_class_id', $student->school_class_id);
                })->where('is_active', true)->get();

                // Add timetable events
                $currentDate = $startDate->clone();
                while ($currentDate <= $endDate) {
                    $dayOfWeek = $currentDate->dayOfWeek;
                    foreach ($timetables as $tt) {
                        if ($tt->day_of_week == $dayOfWeek) {
                            $events[] = [
                                'id' => 'timetable_' . $tt->id . '_' . $currentDate->format('Y-m-d'),
                                'title' => $tt->subject?->name ?? 'Class',
                                'start' => $currentDate->format('Y-m-d') . 'T' . $tt->start_time,
                                'end' => $currentDate->format('Y-m-d') . 'T' . $tt->end_time,
                                'type' => 'class',
                                'color' => '#0d3f38'
                            ];
                        }
                    }
                    $currentDate->addDay();
                }

                // Exams
                $exams = Exam::whereHas('subject', function ($q) use ($student) {
                    $q->whereHas('classes', function ($q2) use ($student) {
                        $q2->where('school_class_id', $student->school_class_id);
                    });
                })->whereBetween('exam_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get();

                foreach ($exams as $exam) {
                    $end = Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->addMinutes($exam->duration_minutes ?? 180);
                    $events[] = [
                        'id' => 'exam_' . $exam->id,
                        'title' => $exam->subject?->name . ' Exam' ?? 'Exam',
                        'start' => Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->format('c'),
                        'end' => $end->format('c'),
                        'type' => 'exam',
                        'color' => '#dc2626'
                    ];
                }

                // Live classes for student's class
                $liveClasses = LiveClass::whereHas('subject', function ($q) use ($student) {
                    $q->whereHas('classes', function ($q2) use ($student) {
                        $q2->where('school_class_id', $student->school_class_id);
                    });
                })->whereBetween('scheduled_at', [$startDate, $endDate])->get();

                foreach ($liveClasses as $lc) {
                    if ($lc->scheduled_at) {
                        $end = $lc->scheduled_at->clone()->addMinutes($lc->duration_minutes ?? 60);
                        $events[] = [
                            'id' => 'liveclass_' . $lc->id,
                            'title' => $lc->title ?? 'Live Class',
                            'start' => $lc->scheduled_at->format('c'),
                            'end' => $end->format('c'),
                            'type' => 'live',
                            'color' => '#1a5c54'
                        ];
                    }
                }

                // Assignments with due dates
                $assignments = Assignment::whereHas('subject', function ($q) use ($student) {
                    $q->whereHas('classes', function ($q2) use ($student) {
                        $q2->where('school_class_id', $student->school_class_id);
                    });
                })->whereBetween('due_date', [$startDate, $endDate])->get();

                foreach ($assignments as $assignment) {
                    $events[] = [
                        'id' => 'assignment_' . $assignment->id,
                        'title' => $assignment->title ?? 'Assignment Due',
                        'start' => $assignment->due_date->format('Y-m-d'),
                        'type' => 'assignment',
                        'color' => '#f59e0b'
                    ];
                }
            }
        }

        // Parent: Show children's exams and important dates
        elseif ($user->isParent()) {
            return response()->json(['events' => $events]);

            $guardian = Guardian::where('user_id', $user->id)->first();
            if (!$guardian) {
                return response()->json(['events' => $events]);
            }

            $studentIds = $guardian->students()->pluck('id');
                if ($studentIds->isEmpty()) {
                    return response()->json(['events' => $events]);
                }

                $classIds = Student::whereIn('id', $studentIds)
                    ->whereNotNull('school_class_id')
                    ->pluck('school_class_id');

                // Exams for all children
                $exams = Exam::whereIn('school_class_id', $classIds)
                    ->whereBetween('exam_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get();

                foreach ($exams as $exam) {
                    $end = Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->addMinutes($exam->duration_minutes ?? 180);
                    $events[] = [
                        'id' => 'exam_' . $exam->id,
                        'title' => $exam->subject?->name . ' Exam' ?? 'Exam',
                        'start' => Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->format('c'),
                        'end' => $end->format('c'),
                        'type' => 'exam',
                        'color' => '#dc2626'
                    ];
                }

                // Fee payments (if due date exists)
                $fees = FeePayment::whereIn('student_id', $studentIds)
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->get();

                foreach ($fees as $fee) {
                    $events[] = [
                        'id' => 'fee_' . $fee->id,
                        'title' => 'Fee Payment Due',
                        'start' => $fee->payment_date->format('Y-m-d'),
                        'type' => 'fee',
                        'color' => '#8b5cf6'
                    ];
            }
        }

        // Admin/Staff/Super Admin: Show institutional events and exams
        elseif ($user->isAdmin() || $user->isStaff() || $user->isSuperAdmin()) {
            // All exams
            $exams = Exam::whereBetween('exam_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            foreach ($exams as $exam) {
                $end = Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->addMinutes($exam->duration_minutes ?? 180);
                $events[] = [
                    'id' => 'exam_' . $exam->id,
                    'title' => $exam->subject?->name . ' Exam' ?? 'Exam',
                    'start' => Carbon::parse($exam->exam_date . ' ' . $exam->start_time)->format('c'),
                    'end' => $end->format('c'),
                    'type' => 'exam',
                    'color' => '#dc2626'
                ];
            }

            // Institutional events (if Event model is in use)
            $institutionalEvents = Event::whereBetween('starts_at', [$startDate, $endDate])
                ->where('is_active', true)
                ->get();

            foreach ($institutionalEvents as $event) {
                $end = $event->ends_at ?? $event->starts_at->addHours(1);
                $events[] = [
                    'id' => 'event_' . $event->id,
                    'title' => $event->title ?? 'Event',
                    'start' => $event->starts_at->format('c'),
                    'end' => $end->format('c'),
                    'type' => 'event',
                    'color' => '#06b6d4'
                ];
            }
        }

        return response()->json(['events' => $events]);
    }
}
