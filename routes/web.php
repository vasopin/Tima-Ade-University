<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\LiveClassMonitoringController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestQuestionController;
use App\Http\Controllers\StudentTestController;
use App\Http\Controllers\TeacherTestController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\TimaAiController;
use App\Http\Controllers\AdmissionApplicationController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdministrativeRoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\VoiceMessageController;
use App\Http\Controllers\TranscriptController;
use App\Http\Controllers\AcademicStandingController;
use App\Http\Controllers\GraduationRecordController;
use App\Http\Controllers\ExamApprovalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayrollRecordController;
use App\Http\Controllers\HrWorkspaceController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LibraryMemberController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BookReservationController;
use App\Http\Controllers\StaffWorkspaceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdvisingController;
use App\Http\Controllers\AcademicFoundationController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\CourseCompletionController;
use App\Http\Controllers\LmsDiscussionController;
use App\Http\Controllers\QuizFoundationController;
use App\Http\Controllers\DeanAcademicController;
use App\Http\Controllers\ExecutiveReadOnlyController;
use App\Http\Controllers\DepartmentHeadController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    Route::get('/', [PublicController::class, 'home'])->name('home');
    Route::get('/about',             [PublicController::class, 'about'])->name('about');
    Route::get('/about/board',       [PublicController::class, 'aboutBoard'])->name('about.board');
    Route::get('/about/campuses',    [PublicController::class, 'aboutCampuses'])->name('about.campuses');
    Route::get('/academics',         [PublicController::class, 'academics'])->name('academics');
    Route::get('/programs',          [PublicController::class, 'programs'])->name('programs');
    Route::get('/programs/calendar', [PublicController::class, 'programCalendar'])->name('programs.calendar');
    Route::get('/programs/careers-certifications', [PublicController::class, 'programCareers'])->name('programs.careers');
    Route::get('/facilities',        [PublicController::class, 'facilities'])->name('facilities');
    Route::get('/e-campus/learning', [PublicController::class, 'eCampusLearning'])->name('e-campus.learning');
    Route::get('/e-campus/resources', [PublicController::class, 'eCampusResources'])->name('e-campus.resources');
    Route::get('/e-campus/student-services', [PublicController::class, 'eCampusStudentServices'])->name('e-campus.student-services');
    Route::get('/e-campus/help', [PublicController::class, 'eCampusHelp'])->name('e-campus.help');
    Route::get('/faculty',           [PublicController::class, 'faculty'])->name('faculty');
    Route::get('/admissions',        [PublicController::class, 'admissions'])->name('admissions');
    Route::get('/apply', [AdmissionApplicationController::class, 'create'])->name('apply');
    Route::post('/apply/basic', [AdmissionApplicationController::class, 'storeBasic'])->name('apply.basic');
    Route::post('/apply/education', [AdmissionApplicationController::class, 'storeEducation'])->name('apply.education');
    Route::post('/apply/documents', [AdmissionApplicationController::class, 'storeDocuments'])->name('apply.documents');
    Route::post('/apply/submit', [AdmissionApplicationController::class, 'submit'])->name('apply.submit');
    Route::get('/admissions/how-to-apply', [PublicController::class, 'admissionsHowToApply'])->name('admissions.how-to-apply');
    Route::get('/admissions/requirements', [PublicController::class, 'admissionsRequirements'])->name('admissions.requirements');
    Route::get('/admissions/application-process', [PublicController::class, 'admissionsProcess'])->name('admissions.process');
    Route::get('/admissions/dates', [PublicController::class, 'admissionsDates'])->name('admissions.dates');
    Route::get('/admissions/fees', [PublicController::class, 'admissionsFees'])->name('admissions.fees');
    Route::get('/admissions/international', [PublicController::class, 'admissionsInternational'])->name('admissions.international');
    Route::get('/admissions/faq', [PublicController::class, 'admissionsFaq'])->name('admissions.faq');
    Route::post('/admissions/apply', [PublicController::class, 'storeAdmission'])->name('admissions.apply');
    Route::get('/contact',           [PublicController::class, 'contact'])->name('contact');
    Route::get('/search',            [PublicController::class, 'search'])->name('search');
    Route::post('/contact/send',     [PublicController::class, 'storeContact'])->name('contact.send');
    Route::get('/notices',           [PublicController::class, 'notices'])->name('notices');
    Route::get('/notices/{notice}',  [PublicController::class, 'noticeSingle'])->name('notices.single');
    Route::get('/student-life',      [PublicController::class, 'studentLife'])->name('student-life');
    Route::get('/student-life/alumni', [PublicController::class, 'studentLifeAlumni'])->name('student-life.alumni');
    Route::get('/student-life/research', [PublicController::class, 'studentLifeResearch'])->name('student-life.research');
    Route::get('/student-life/news', [PublicController::class, 'studentLifeNews'])->name('student-life.news');
    Route::get('/leadership',        [PublicController::class, 'leadership'])->name('leadership');
    Route::get('/research',          [PublicController::class, 'research'])->name('research');
    Route::get('/news',              [PublicController::class, 'news'])->name('news');
    Route::get('/events',            [PublicController::class, 'events'])->name('events');
    Route::get('/scholarships',      [PublicController::class, 'scholarships'])->name('scholarships');
    Route::get('/careers',           [PublicController::class, 'careers'])->name('careers');
    Route::get('/alumni',            [PublicController::class, 'alumni'])->name('alumni');
    Route::get('/library',           [PublicController::class, 'library'])->name('library');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',                 [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',                [AuthController::class, 'login'])->name('login.post');
    Route::post('/portal/login', [AuthController::class, 'login'])->name('university.login.post');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::get('/register',              [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',             [AuthController::class, 'register'])->name('register.post');

    // Password Recovery
    Route::get('/forgot-password',       [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',      [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}',[AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',       [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::get('/portal/login', [AuthController::class, 'showLogin'])->name('university.login');
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::redirect('/university-portal/login', '/portal/login', 301)->name('legacy.university.login');
Route::redirect('/university-admin/login', '/admin/login', 301)->name('legacy.admin.login');
Route::post('/university-portal/login', [AuthController::class, 'login'])->name('legacy.university.login.post');
Route::post('/university-admin/login', [AuthController::class, 'login'])->name('legacy.admin.login.post');

Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/hemis', [PublicController::class, 'hemis'])
    ->name('hemis')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Management System & Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard (Role-driven: Super Admin / Admin / Teacher / Student / Parent)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/welcome', [DashboardController::class, 'welcome'])->name('dashboard.welcome');
    Route::get('/welcome', [DashboardController::class, 'welcome'])->name('welcome');
    Route::get('/student/welcome', [DashboardController::class, 'studentWelcome'])->name('student.welcome');
    Route::get('/teacher/welcome', [DashboardController::class, 'teacherWelcome'])->name('teacher.welcome');
    Route::get('/parent/welcome', [DashboardController::class, 'parentWelcome'])->name('parent.welcome');
    Route::get('/staff/welcome', [DashboardController::class, 'staffWelcome'])->name('staff.welcome');
    Route::get('/executive/welcome', [DashboardController::class, 'executiveWelcome'])->name('executive.welcome');
    Route::get('/dean/welcome', [DashboardController::class, 'deanWelcome'])->name('dean.welcome');
    Route::get('/department-head/welcome', [DashboardController::class, 'departmentHeadWelcome'])->name('department-head.welcome');
    Route::get('/academic-advisor/welcome', [DashboardController::class, 'academicAdvisorWelcome'])->name('academic-advisor.welcome');
    Route::get('/admissions-officer/welcome', [DashboardController::class, 'admissionsOfficerWelcome'])->name('admissions_officer.welcome');
    Route::get('/finance-officer/welcome', [DashboardController::class, 'financeOfficerWelcome'])->name('finance_officer.welcome');
    Route::get('/registrar/welcome', [DashboardController::class, 'registrarWelcome'])->name('registrar.welcome');
    Route::get('/hr-officer/welcome', [DashboardController::class, 'hrOfficerWelcome'])->name('hr_officer.welcome');
    Route::get('/librarian/welcome', [DashboardController::class, 'librarianWelcome'])->name('librarian.welcome');
    Route::prefix('department-head')->name('department-head.')->group(function () {
        Route::get('/programs', [DepartmentHeadController::class, 'programs'])->name('programs');
        Route::get('/courses', [DepartmentHeadController::class, 'courses'])->name('courses');
        Route::get('/sections', [DepartmentHeadController::class, 'sections'])->name('sections');
        Route::get('/students', [DepartmentHeadController::class, 'students'])->name('students');
        Route::get('/instructors', [DepartmentHeadController::class, 'instructors'])->name('instructors');
        Route::get('/academic-performance', [DepartmentHeadController::class, 'academicPerformance'])->name('academic-performance');
        Route::get('/advising', [DepartmentHeadController::class, 'advising'])->name('advising');
        Route::get('/analytics', [DepartmentHeadController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [DepartmentHeadController::class, 'reports'])->name('reports');
    });
    Route::prefix('executive')->name('executive.')->group(function () {
        Route::get('/faculties', [ExecutiveReadOnlyController::class, 'faculties'])->name('faculties');
        Route::get('/departments', [ExecutiveReadOnlyController::class, 'departments'])->name('departments');
        Route::get('/programs', [ExecutiveReadOnlyController::class, 'programs'])->name('programs');
        Route::get('/students', [ExecutiveReadOnlyController::class, 'students'])->name('students');
        Route::get('/academic-performance', [ExecutiveReadOnlyController::class, 'academicPerformance'])->name('academic-performance');
        Route::get('/analytics', [ExecutiveReadOnlyController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [ExecutiveReadOnlyController::class, 'reports'])->name('reports');
    });
    Route::middleware('auth')->prefix('dean')->name('dean.')->group(function () {
        Route::get('/departments', [DeanAcademicController::class, 'departments'])->name('departments');
        Route::get('/departments/{department}', [DeanAcademicController::class, 'department'])->name('departments.show');
        Route::get('/programs', [DeanAcademicController::class, 'programs'])->name('programs');
        Route::get('/courses', [DeanAcademicController::class, 'courses'])->name('courses');
        Route::get('/sections', [DeanAcademicController::class, 'sections'])->name('sections');
        Route::get('/students', [DeanAcademicController::class, 'students'])->name('students');
        Route::get('/students/export', [DeanAcademicController::class, 'exportStudents'])->name('students.export');
        Route::get('/staff', [DeanAcademicController::class, 'staff'])->name('staff');
        Route::get('/analytics', [DeanAcademicController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [DeanAcademicController::class, 'reports'])->name('reports');
    });
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('tenants');
        Route::get('/roles', [SuperAdminController::class, 'roles'])->name('roles');
        Route::get('/health', [SuperAdminController::class, 'health'])->name('health');
    });

    Route::prefix('staff-workspace')->name('staff.workspace.')->group(function () {
        Route::get('/communications', [StaffWorkspaceController::class, 'communications'])->name('communications');
        Route::get('/time', [StaffWorkspaceController::class, 'time'])->name('time');
        Route::post('/time/check-in', [StaffWorkspaceController::class, 'clockIn'])->name('time.check-in');
        Route::post('/time/check-out', [StaffWorkspaceController::class, 'clockOut'])->name('time.check-out');
        Route::get('/support', [StaffWorkspaceController::class, 'workItems'])->defaults('type', 'support')->name('support');
        Route::post('/support', [StaffWorkspaceController::class, 'storeWorkItem'])->defaults('type', 'support')->name('support.store');
        Route::get('/maintenance', [StaffWorkspaceController::class, 'workItems'])->defaults('type', 'maintenance')->name('maintenance');
        Route::post('/maintenance', [StaffWorkspaceController::class, 'storeWorkItem'])->defaults('type', 'maintenance')->name('maintenance.store');
        Route::get('/hr', [StaffWorkspaceController::class, 'hr'])->name('hr');
        Route::get('/hr/payslips/{payrollRecord}/download', [StaffWorkspaceController::class, 'downloadPayslip'])->name('hr.payslips.download');
    });
    
    // Search (role-based)
    Route::get('/api/search', [SearchController::class, 'search'])->name('search');
    
    // Calendar (role-based events)
    Route::get('/api/calendar/events', [DashboardController::class, 'getCalendarEvents'])->name('calendar.events');

    // Private messaging and student friendship
    Route::get('/messages', [MessagingController::class, 'index'])->name('messages.index');
    Route::get('/api/messages/conversations', [MessagingController::class, 'conversations'])->name('messages.conversations');
    Route::post('/api/messages/conversations', [MessagingController::class, 'start'])->name('messages.conversations.start');
    Route::get('/api/messages/conversations/{conversation}/messages', [MessagingController::class, 'show'])->name('messages.conversations.show');
    Route::post('/api/messages/conversations/{conversation}/messages', [MessagingController::class, 'send'])->name('messages.send');
    Route::delete('/api/messages/{message}', [MessagingController::class, 'deleteMessage'])->name('messages.delete');
    Route::get('/api/messages/students', [MessagingController::class, 'students'])->name('messages.students');
    Route::get('/api/messages/contacts', [MessagingController::class, 'contacts'])->name('messages.contacts');
    Route::get('/api/messages/friend-requests', [MessagingController::class, 'friendRequests'])->name('messages.friend-requests');
    Route::post('/api/messages/friend-requests', [MessagingController::class, 'sendFriendRequest'])->name('messages.friend-requests.store');
    Route::patch('/api/messages/friend-requests/{friendRequest}/{status}', [MessagingController::class, 'updateFriendRequest'])->name('messages.friend-requests.update');
    Route::delete('/api/messages/friend-requests/{friendRequest}', [MessagingController::class, 'removeFriend'])->name('messages.friend-requests.remove');

    // Voice messages
    Route::post('/api/conversations/{conversation}/voice-messages', [VoiceMessageController::class, 'store'])->name('voice-messages.store');
    Route::get('/api/voice-messages/{voiceMessage}/file', [VoiceMessageController::class, 'file'])->name('voice-messages.file');
    Route::patch('/api/voice-messages/{voiceMessage}/played', [VoiceMessageController::class, 'markAsPlayed'])->name('voice-messages.played');

    Route::post('/hemis/lookup', [PublicController::class, 'hemisLookup'])->name('hemis.lookup');
    Route::get('/admin/applications', [AdmissionApplicationController::class, 'index'])->name('admin.applications');
    Route::get('/admin/documents', [AdmissionApplicationController::class, 'documents'])->name('admin.documents');
    Route::get('/admin/reviews', [AdmissionApplicationController::class, 'reviews'])->name('admin.reviews');
    Route::get('/admin/interviews', [AdmissionApplicationController::class, 'interviews'])->name('admin.interviews');
    Route::get('/admin/decisions', [AdmissionApplicationController::class, 'decisions'])->name('admin.decisions');
    Route::get('/admin/applicants', [AdmissionApplicationController::class, 'applicants'])->name('admin.applicants');
    Route::get('/admin/applicants/{application}', [AdmissionApplicationController::class, 'applicant'])->name('admin.applicants.show');
    Route::get('/admin/reports', [AdmissionApplicationController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/communications', [AdmissionApplicationController::class, 'communications'])->name('admin.communications');
    Route::get('/admin/applications/{application}', [AdmissionApplicationController::class, 'show'])->name('admin.applications.show');
    Route::get('/admin/applications/{application}/documents/{document}', [AdmissionApplicationController::class, 'document'])->whereNumber('document')->name('admin.applications.document');
    Route::patch('/admin/applications/{application}/documents/{document}', [AdmissionApplicationController::class, 'updateDocument'])->whereNumber('document')->name('admin.applications.documents.update');
    Route::post('/admin/applications/{application}/review', [AdmissionApplicationController::class, 'review'])->name('admin.applications.review');
    Route::post('/admin/applications/{application}/approve', [AdmissionApplicationController::class, 'approve'])->name('admin.applications.approve');
    Route::post('/admin/applications/{application}/decline', [AdmissionApplicationController::class, 'decline'])->name('admin.applications.decline');
    Route::post('/admin/applications/{application}/communicate', [AdmissionApplicationController::class, 'sendCommunication'])->name('admin.applications.communicate');
    Route::get('/admin/applications/{application}/decision-letter', [AdmissionApplicationController::class, 'decisionLetter'])->name('admin.applications.decision-letter');
    Route::post('/tima-ai/respond', [TimaAiController::class, 'respond'])->name('tima-ai.respond');
    Route::get('/parent/children', [DashboardController::class, 'parentChildrenPage'])->name('parent.children');
    Route::get('/parent/children/{student}', [DashboardController::class, 'parentChildPage'])->name('parent.child');
    Route::get('/parent/profiles', [ParentPortalController::class, 'profiles'])->name('parent.profiles');
    Route::get('/parent/academics', [ParentPortalController::class, 'academics'])->name('parent.academics');
    Route::get('/parent/finance', [ParentPortalController::class, 'finance'])->name('parent.finance');
    Route::get('/parent/communications', [ParentPortalController::class, 'communications'])->name('parent.communications');
    Route::get('/student/dashboard', [DashboardController::class, 'studentDashboardPage'])->name('student.dashboard');
    Route::get('/student/analytics', [\App\Http\Controllers\LmsAnalyticsController::class, 'student'])->name('student.analytics');
    Route::get('/teacher/analytics', [\App\Http\Controllers\LmsAnalyticsController::class, 'teacher'])->name('teacher.analytics');
    Route::get('/student/gradebook', [GradebookController::class, 'student'])->name('student.gradebook');
    Route::get('/student/enrollments/{enrollment}/completion', [CourseCompletionController::class, 'student'])->name('student.enrollment.completion');
    Route::get('/student/discussions', [LmsDiscussionController::class, 'studentIndex'])->name('student.discussions.index');
    Route::get('/student/discussions/{discussion}', [LmsDiscussionController::class, 'show'])->name('student.discussions.show');
    Route::get('/student/quizzes/{test}', [QuizFoundationController::class, 'studentPreview'])->name('student.quizzes.preview');
    Route::get('/student/live-classes', [DashboardController::class, 'studentLiveClassesPage'])->name('student.live-classes');
    Route::get('/student/live-classes/status', [DashboardController::class, 'studentLiveClassesStatus'])->name('student.live-classes.status');
    Route::get('/student/courses', [DashboardController::class, 'studentCoursesPage'])->name('student.courses');
    Route::get('/student/videos', [DashboardController::class, 'studentVideosPage'])->name('student.videos');
    Route::get('/student/materials', [DashboardController::class, 'studentMaterialsPage'])->name('student.materials');
    Route::get('/student/assignments', [DashboardController::class, 'studentAssignmentsPage'])->name('student.assignments');
    Route::get('/student/attendance', [DashboardController::class, 'studentAttendancePage'])->name('student.attendance');
    Route::get('/student/announcements', [DashboardController::class, 'studentAnnouncementsPage'])->name('student.announcements');
    Route::get('/student/registration', [EnrollmentController::class, 'available'])->name('student.registration');
    Route::post('/student/registration', [EnrollmentController::class, 'store'])->name('student.registration.store');
    Route::post('/student/enrollments/{enrollment}/drop', [EnrollmentController::class, 'drop'])->name('student.enrollments.drop');
    Route::post('/student/enrollments/{enrollment}/withdraw', [EnrollmentController::class, 'withdraw'])->name('student.enrollments.withdraw');
    Route::get('/student/schedule', [StudentPortalController::class, 'schedule'])->name('student.schedule');
    Route::get('/student/academic-records', [StudentPortalController::class, 'academicRecords'])->name('student.academic-records');
    Route::get('/student/fees', [StudentPortalController::class, 'fees'])->name('student.fees');
    Route::get('/student/library', [StudentPortalController::class, 'library'])->name('student.library');
    Route::get('/advising', [AdvisingController::class, 'index'])->name('advising.index');
    Route::get('/advising/dashboard', [AdvisingController::class, 'dashboard'])->name('advising.dashboard');
    Route::post('/advising/appointments', [AdvisingController::class, 'storeAppointment'])->name('advising.appointments.store');
    Route::post('/advising/notes', [AdvisingController::class, 'storeNote'])->name('advising.notes.store');
    Route::post('/advising/assignments', [AdvisingController::class, 'assign'])->name('advising.assignments.store');
    Route::patch('/advising/appointments/{appointment}', [AdvisingController::class, 'updateAppointment'])->name('advising.appointments.update');
    Route::redirect('/student', '/student/dashboard');

    /*
    |------------------------------------------------------------------
    | Tests/Exams Module
    |------------------------------------------------------------------
    */
    // Student test routes
    Route::get('/student/tests', [StudentTestController::class, 'index'])->name('student.tests.index');
    Route::get('/student/tests/{test}', [StudentTestController::class, 'show'])->name('student.tests.show');
    Route::post('/student/tests/{test}/start', [StudentTestController::class, 'start'])->name('student.tests.start');
    Route::get('/student/tests/attempt/{attempt}', [StudentTestController::class, 'attempt'])->name('student.tests.attempt');
    Route::post('/student/tests/attempt/{attempt}/answer', [StudentTestController::class, 'saveAnswer'])->name('student.tests.save-answer');
    Route::post('/student/tests/attempt/{attempt}/submit', [StudentTestController::class, 'submit'])->name('student.tests.submit');
    Route::get('/student/tests/{test}/results', [StudentTestController::class, 'results'])->name('student.tests.results');

    // Teacher test management
    Route::get('/teacher/tests', [TestController::class, 'index'])->name('teacher.tests.index');
    Route::get('/teacher/tests/create', [TestController::class, 'create'])->name('teacher.tests.create');
    Route::post('/teacher/tests', [TestController::class, 'store'])->name('teacher.tests.store');
    Route::get('/teacher/tests/{test}/edit', [TestController::class, 'edit'])->name('teacher.tests.edit');
    Route::put('/teacher/tests/{test}', [TestController::class, 'update'])->name('teacher.tests.update');
    Route::get('/teacher/tests/{test}', [TestController::class, 'show'])->name('teacher.tests.show');
    Route::post('/teacher/tests/{test}/publish', [TestController::class, 'publish'])->name('teacher.tests.publish');
    Route::post('/teacher/tests/{test}/close', [TestController::class, 'close'])->name('teacher.tests.close');
    Route::delete('/teacher/tests/{test}', [TestController::class, 'destroy'])->name('teacher.tests.destroy');

    // Teacher test question management
    Route::post('/teacher/tests/{test}/questions', [TestQuestionController::class, 'store'])->name('teacher.tests.questions.store');
    Route::put('/teacher/questions/{question}', [TestQuestionController::class, 'update'])->name('teacher.tests.questions.update');
    Route::delete('/teacher/questions/{question}', [TestQuestionController::class, 'destroy'])->name('teacher.tests.questions.destroy');
    Route::post('/teacher/questions/{question}/options', [TestQuestionController::class, 'storeOption'])->name('teacher.tests.options.store');
    Route::put('/teacher/options/{option}', [TestQuestionController::class, 'updateOption'])->name('teacher.tests.options.update');
    Route::delete('/teacher/options/{option}', [TestQuestionController::class, 'destroyOption'])->name('teacher.tests.options.destroy');

    // Teacher grading
    Route::get('/teacher/tests/{test}/submissions', [TeacherTestController::class, 'submissions'])->name('teacher.tests.submissions');
    Route::get('/teacher/attempts/{attempt}/grade', [TeacherTestController::class, 'gradeSubmission'])->name('teacher.tests.grade-submission');
    Route::post('/teacher/answers/{answer}/grade', [TeacherTestController::class, 'saveGrade'])->name('teacher.tests.save-grade');
    Route::post('/teacher/attempts/{attempt}/finalize', [TeacherTestController::class, 'finalizeGrade'])->name('teacher.tests.finalize-grade');
    Route::post('/teacher/tests/{test}/release-results', [TeacherTestController::class, 'releaseResults'])->name('teacher.tests.release-results');

    Route::get('/teacher/classes', [DashboardController::class, 'teacherClassesPage'])->name('teacher.classes');
    Route::get('/teacher/courses', [DashboardController::class, 'teacherCoursesPage'])->name('teacher.courses');
    Route::get('/teacher/videos', [DashboardController::class, 'teacherVideosPage'])->name('teacher.videos');
    Route::get('/teacher/materials', [DashboardController::class, 'teacherMaterialsPage'])->name('teacher.materials');
    Route::get('/teacher/attendance', [DashboardController::class, 'teacherAttendancePage'])->name('teacher.attendance');
    Route::get('/teacher/live-classes', [LiveClassController::class, 'index'])->name('teacher.live-classes.index');
    Route::post('/teacher/live-classes/create', [LiveClassController::class, 'create'])->name('teacher.live-classes.create');
    Route::post('/teacher/live-classes', [LiveClassController::class, 'store'])->name('teacher.live-classes.store');
    Route::patch('/live-classes/{liveClass}/schedule', [LiveClassController::class, 'schedule'])->name('live-classes.schedule');
    Route::get('/admin/live-classes', [LiveClassMonitoringController::class, 'index'])->name('admin.live-classes.index');
    Route::post('/live-classes/{liveClass}/start', [LiveClassController::class, 'start'])->name('live-classes.start');
    Route::post('/live-classes/{liveClass}/end', [LiveClassController::class, 'end'])->name('live-classes.end');
    Route::post('/live-classes/{liveClass}/join', [LiveClassController::class, 'join'])->name('live-classes.join');
    Route::post('/live-classes/{liveClass}/leave', [LiveClassController::class, 'leave'])->name('live-classes.leave');
    Route::patch('/live-classes/{liveClass}/media', [LiveClassController::class, 'updateMedia'])->name('live-classes.media.update');
    Route::post('/live-classes/{liveClass}/participants/{participant}/mute', [LiveClassController::class, 'muteParticipant'])->name('live-classes.participants.mute');
    Route::post('/live-classes/{liveClass}/participants/{participant}/remove', [LiveClassController::class, 'removeParticipant'])->name('live-classes.participants.remove');
    Route::patch('/live-classes/{liveClass}/participants/{participant}/permissions', [LiveClassController::class, 'updateParticipantPermissions'])->name('live-classes.participants.permissions');
    Route::get('/live-classes/{liveClass}', [LiveClassController::class, 'show'])->name('live-classes.show');
    Route::post('/live-classes/{liveClass}/messages', [LiveClassController::class, 'storeMessage'])->name('live-classes.messages.store');

    /*
    |------------------------------------------------------------------
    | Profile & Security
    |------------------------------------------------------------------
    */
    Route::get('/profile',            [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',   [ProfileController::class, 'changePassword'])->name('profile.password');

    // Notifications (web)
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',  [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    /*
    |------------------------------------------------------------------
    | User Management (Admin Only)
    |------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/activate',       [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate',     [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/suspend',        [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/approve',        [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    /*
    |------------------------------------------------------------------
    | Staff Management Module
    |------------------------------------------------------------------
    */
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{user}', [StaffController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{user}', [StaffController::class, 'update'])->name('update');
    });

    /*
    |------------------------------------------------------------------
    | Parents & Guardians Module
    |------------------------------------------------------------------
    */
    Route::resource('parents', ParentController::class);

    /*
    |------------------------------------------------------------------
    | Students Module
    |------------------------------------------------------------------
    */
    Route::get('/student-360', [\App\Http\Controllers\Student360Controller::class, 'index'])->name('students.student-360.index');
    Route::get('/students/{student}/360', [\App\Http\Controllers\Student360Controller::class, 'show'])->name('students.student-360');
    Route::post('/students/{student}/lifecycle-status', [\App\Http\Controllers\Student360Controller::class, 'status'])->name('students.lifecycle-status');
    Route::post('/students/{student}/holds', [\App\Http\Controllers\Student360Controller::class, 'hold'])->name('students.holds.store');
    Route::post('/student-holds/{hold}/release', [\App\Http\Controllers\Student360Controller::class, 'releaseHold'])->name('students.holds.release');
    Route::post('/students/{student}/transfers', [\App\Http\Controllers\Student360Controller::class, 'transfer'])->name('students.transfers.store');
    Route::post('/student-transfers/{transfer}/review', [\App\Http\Controllers\Student360Controller::class, 'reviewTransfer'])->name('students.transfers.review');
    Route::resource('students', StudentController::class);
    Route::get('/api/sections', [StudentController::class, 'getSections'])->name('api.sections');

    /*
    |------------------------------------------------------------------
    | Teachers Module
    |------------------------------------------------------------------
    */
    Route::resource('teachers', TeacherController::class);

    foreach ([
        'admissions-officers' => \App\Models\Role::ADMISSIONS_OFFICER,
        'finance-officers' => \App\Models\Role::FINANCE_OFFICER,
        'registrars' => \App\Models\Role::REGISTRAR,
        'hr-officers' => \App\Models\Role::HR_OFFICER,
        'librarians' => \App\Models\Role::LIBRARIAN,
        'presidents' => \App\Models\Role::PRESIDENT,
        'chancellors' => \App\Models\Role::CHANCELLOR,
        'deans' => \App\Models\Role::DEAN,
        'department-heads' => \App\Models\Role::DEPARTMENT_HEAD,
        'academic-advisors' => \App\Models\Role::ACADEMIC_ADVISOR,
    ] as $path => $roleSlug) {
        Route::get("/{$path}", [AdministrativeRoleController::class, 'index'])->defaults('role', $roleSlug)->name("{$roleSlug}.index");
        Route::get("/{$path}/create", [AdministrativeRoleController::class, 'create'])->defaults('role', $roleSlug)->name("{$roleSlug}.create");
        Route::post("/{$path}", [AdministrativeRoleController::class, 'store'])->defaults('role', $roleSlug)->name("{$roleSlug}.store");
        Route::get("/{$path}/{user}", [AdministrativeRoleController::class, 'show'])->defaults('role', $roleSlug)->name("{$roleSlug}.show");
        Route::get("/{$path}/{user}/edit", [AdministrativeRoleController::class, 'edit'])->defaults('role', $roleSlug)->name("{$roleSlug}.edit");
        Route::put("/{$path}/{user}", [AdministrativeRoleController::class, 'update'])->defaults('role', $roleSlug)->name("{$roleSlug}.update");
        Route::delete("/{$path}/{user}", [AdministrativeRoleController::class, 'destroy'])->defaults('role', $roleSlug)->name("{$roleSlug}.destroy");
    }

    /*
    |------------------------------------------------------------------
    | Learning Materials & LMS Uploads
    |------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::get('/teacher/gradebook', [GradebookController::class, 'teacher'])->name('teacher.gradebook');
        Route::get('/teacher/course-sections/{section}/completion', [CourseCompletionController::class, 'teacher'])->name('teacher.course-section.completion');
        Route::get('/teacher/discussions', [LmsDiscussionController::class, 'teacherIndex'])->name('teacher.discussions.index');
        Route::get('/teacher/question-bank', [QuizFoundationController::class, 'bank'])->name('teacher.question-bank.index');
        Route::post('/teacher/question-bank', [QuizFoundationController::class, 'storeQuestion'])->name('teacher.question-bank.store');
        Route::post('/teacher/tests/{test}/question-bank/{question}', [QuizFoundationController::class, 'attach'])->name('teacher.tests.question-bank.attach');
        Route::delete('/teacher/tests/{test}/question-bank/{question}', [QuizFoundationController::class, 'detach'])->name('teacher.tests.question-bank.detach');
        Route::get('/teacher/tests/{test}/preview', [QuizFoundationController::class, 'teacherPreview'])->name('teacher.tests.preview');
        Route::post('/teacher/tests/{test}/question-pools', [QuizFoundationController::class, 'storePool'])->name('teacher.tests.question-pools.store');
        Route::delete('/teacher/question-pools/{pool}', [QuizFoundationController::class, 'detachPool'])->name('teacher.tests.question-pools.destroy');
        Route::get('/teacher/discussions/create', [LmsDiscussionController::class, 'create'])->name('teacher.discussions.create');
        Route::post('/teacher/discussions', [LmsDiscussionController::class, 'store'])->name('teacher.discussions.store');
        Route::get('/teacher/discussions/{discussion}', [LmsDiscussionController::class, 'show'])->name('teacher.discussions.show');
        Route::post('/teacher/discussions/{discussion}/close', [LmsDiscussionController::class, 'close'])->name('teacher.discussions.close');
        Route::post('/lms/discussions/{discussion}/posts', [LmsDiscussionController::class, 'post'])->name('lms.discussions.posts.store');
        Route::post('/lms/discussion-posts/{post}/moderate', [LmsDiscussionController::class, 'moderate'])->name('lms.discussion-posts.moderate');
        Route::put('/lms/discussion-posts/{post}', [LmsDiscussionController::class, 'updatePost'])->name('lms.discussion-posts.update');
        Route::post('/teacher/learning/videos', [LearningMaterialController::class, 'storeVideo'])->name('teacher.learning.video.store');
        Route::post('/teacher/learning/materials', [LearningMaterialController::class, 'storeMaterial'])->name('teacher.learning.material.store');
        Route::post('/teacher/learning/assignments', [LearningMaterialController::class, 'storeAssignment'])->name('teacher.learning.assignment.store');
        Route::post('/teacher/learning/assignments/{assignment}/rubric', [\App\Http\Controllers\AssignmentSubmissionController::class, 'storeRubric'])->name('teacher.learning.assignment.rubric.store');
        Route::post('/student/assignments/{assignment}/submissions', [\App\Http\Controllers\AssignmentSubmissionController::class, 'store'])->name('student.assignments.submit');
        Route::put('/student/submissions/{submission}', [\App\Http\Controllers\AssignmentSubmissionController::class, 'updateDraft'])->name('student.assignments.submissions.update');
        Route::get('/teacher/learning/assignments/{assignment}/submissions', [\App\Http\Controllers\AssignmentSubmissionController::class, 'index'])->name('teacher.learning.assignment.submissions');
        Route::post('/teacher/learning/submissions/{submission}/grade', [\App\Http\Controllers\AssignmentSubmissionController::class, 'grade'])->name('teacher.learning.submission.grade');
        Route::delete('/teacher/learning/videos/{video}', [LearningMaterialController::class, 'destroyVideo'])->name('teacher.learning.video.delete');
        Route::delete('/teacher/learning/materials/{material}', [LearningMaterialController::class, 'destroyMaterial'])->name('teacher.learning.material.delete');
        Route::delete('/teacher/learning/assignments/{assignment}', [LearningMaterialController::class, 'destroyAssignment'])->name('teacher.learning.assignment.delete');
        Route::post('/attendance', [AttendanceController::class, 'store']);
    });

    Route::get('/learning/videos/{video}/file', [LearningMaterialController::class, 'viewVideo'])->name('learning.video.file');
    Route::get('/learning/materials/{material}/file', [LearningMaterialController::class, 'viewMaterial'])->name('learning.material.file');
    Route::get('/learning/assignments/{assignment}/file', [LearningMaterialController::class, 'viewAssignment'])->name('learning.assignment.file');
    Route::get('/learning/submissions/{submission}/file', [\App\Http\Controllers\AssignmentSubmissionController::class, 'file'])->name('learning.submission.file');

    /*
    |------------------------------------------------------------------
    | Registrar Module: Transcripts, Academic Standing, Graduation,
    | Examination Results Approval
    |------------------------------------------------------------------
    */
    Route::get('/my-transcripts', [TranscriptController::class, 'myTranscripts'])->name('transcripts.mine');

    Route::prefix('admin/academics')->name('admin.academics.')->group(function () {
        Route::get('/', [AcademicFoundationController::class, 'index'])->name('index');
        Route::post('/campuses', [AcademicFoundationController::class, 'storeCampus'])->name('campuses.store');
        Route::post('/faculties', [AcademicFoundationController::class, 'storeFaculty'])->name('faculties.store');
        Route::post('/departments', [AcademicFoundationController::class, 'storeDepartment'])->name('departments.store');
        Route::post('/programs', [AcademicFoundationController::class, 'storeProgram'])->name('programs.store');
        Route::put('/programs/{program}', [AcademicFoundationController::class, 'updateProgram'])->name('programs.update');
        Route::post('/academic-years', [AcademicFoundationController::class, 'storeAcademicYear'])->name('years.store');
        Route::post('/terms', [AcademicFoundationController::class, 'storeTerm'])->name('terms.store');
        Route::post('/courses', [AcademicFoundationController::class, 'storeCourse'])->name('courses.store');
        Route::post('/prerequisites', [AcademicFoundationController::class, 'storePrerequisite'])->name('prerequisites.store');
        Route::post('/curricula', [AcademicFoundationController::class, 'storeCurriculum'])->name('curricula.store');
        Route::post('/curricula/{curriculum}/courses', [AcademicFoundationController::class, 'attachCourse'])->name('curricula.courses.store');
        Route::post('/sections', [AcademicFoundationController::class, 'storeSection'])->name('sections.store');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    });

    Route::prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/enrollment', [RegistrarController::class, 'enrollment'])->name('enrollment');
        Route::get('/enrollment/records', [EnrollmentController::class, 'history'])->name('enrollment.records');
        Route::get('/certificates', [RegistrarController::class, 'certificates'])->name('certificates');
        Route::get('/transfer-credits', [RegistrarController::class, 'transferCredits'])->name('transfer-credits');
        Route::get('/transcripts', [TranscriptController::class, 'index'])->name('transcripts.index');
        Route::get('/transcripts/create', [TranscriptController::class, 'create'])->name('transcripts.create');
        Route::post('/transcripts', [TranscriptController::class, 'store'])->name('transcripts.store');
        Route::get('/transcripts/{transcript}', [TranscriptController::class, 'show'])->name('transcripts.show');

        Route::get('/academic-standing', [AcademicStandingController::class, 'index'])->name('academic-standing.index');
        Route::get('/academic-standing/{student}/edit', [AcademicStandingController::class, 'edit'])->name('academic-standing.edit');
        Route::put('/academic-standing/{student}', [AcademicStandingController::class, 'update'])->name('academic-standing.update');

        Route::get('/graduation', [GraduationRecordController::class, 'index'])->name('graduation.index');
        Route::get('/graduation/create', [GraduationRecordController::class, 'create'])->name('graduation.create');
        Route::post('/graduation', [GraduationRecordController::class, 'store'])->name('graduation.store');
        Route::post('/graduation/{graduationRecord}/approve', [GraduationRecordController::class, 'approve'])->name('graduation.approve');

        Route::get('/exam-approvals', [ExamApprovalController::class, 'index'])->name('exam-approvals.index');
        Route::post('/exam-approvals/{exam}/approve', [ExamApprovalController::class, 'approve'])->name('exam-approvals.approve');
        Route::post('/exam-approvals/{exam}/reject', [ExamApprovalController::class, 'reject'])->name('exam-approvals.reject');
        Route::post('/exam-approvals/{exam}/publish', [ExamApprovalController::class, 'publish'])->name('exam-approvals.publish');
    });

    /*
    |------------------------------------------------------------------
    | Human Resources Module: Employees, Leave, Payroll
    |------------------------------------------------------------------
    */
    Route::get('/my-leave-requests', [LeaveRequestController::class, 'mine'])->name('leave-requests.mine');
    Route::post('/my-leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');

    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/recruitment', [HrWorkspaceController::class, 'recruitment'])->name('recruitment');
        Route::get('/performance', [HrWorkspaceController::class, 'performance'])->name('performance');
        Route::get('/attendance', [HrWorkspaceController::class, 'attendance'])->name('attendance');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

        Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
        Route::post('/leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
        Route::put('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');

        Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::post('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

        Route::get('/payroll-periods', [PayrollPeriodController::class, 'index'])->name('payroll-periods.index');
        Route::post('/payroll-periods', [PayrollPeriodController::class, 'store'])->name('payroll-periods.store');
        Route::post('/payroll-periods/{payrollPeriod}/close', [PayrollPeriodController::class, 'close'])->name('payroll-periods.close');

        Route::get('/payroll-records', [PayrollRecordController::class, 'index'])->name('payroll-records.index');
        Route::get('/payroll', [PayrollRecordController::class, 'index'])->name('payroll');
        Route::post('/payroll-records', [PayrollRecordController::class, 'store'])->name('payroll-records.store');
        Route::post('/payroll-records/{payrollRecord}/process', [PayrollRecordController::class, 'process'])->name('payroll-records.process');
        Route::post('/payroll-records/{payrollRecord}/mark-paid', [PayrollRecordController::class, 'markPaid'])->name('payroll-records.mark-paid');
    });

    /*
    |------------------------------------------------------------------
    | Library Module: Books, Members, Borrowing, Reservations
    |------------------------------------------------------------------
    */
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/books', [BookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        Route::get('/members', [LibraryMemberController::class, 'index'])->name('members.index');
        Route::get('/members/create', [LibraryMemberController::class, 'create'])->name('members.create');
        Route::post('/members', [LibraryMemberController::class, 'store'])->name('members.store');
        Route::post('/members/{member}/activate', [LibraryMemberController::class, 'activate'])->name('members.activate');
        Route::post('/members/{member}/deactivate', [LibraryMemberController::class, 'deactivate'])->name('members.deactivate');

        Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
        Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
        Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');
        Route::post('/borrowings/mark-overdue', [BorrowingController::class, 'markOverdue'])->name('borrowings.mark-overdue');

        Route::get('/reservations', [BookReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations', [BookReservationController::class, 'store'])->name('reservations.store');
        Route::post('/reservations/{reservation}/fulfil', [BookReservationController::class, 'fulfil'])->name('reservations.fulfil');
        Route::post('/reservations/{reservation}/cancel', [BookReservationController::class, 'cancel'])->name('reservations.cancel');
    });

    /*
    |------------------------------------------------------------------
    | Classes & Sections Module
    |------------------------------------------------------------------
    */
    Route::resource('classes', ClassController::class);

    /*
    |------------------------------------------------------------------
    | Subjects Module
    |------------------------------------------------------------------
    */
    Route::resource('subjects', SubjectController::class)->except(['show']);

    /*
    |------------------------------------------------------------------
    | Timetable & Scheduling Module
    |------------------------------------------------------------------
    */
    Route::resource('timetables', TimetableController::class)->except(['show']);

    Route::get('/admin/notices/videos', [NoticeController::class, 'videos'])
        ->name('admin.notices.videos');

    /*
    |------------------------------------------------------------------
    | Attendance Module
    |------------------------------------------------------------------
    */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',       [AttendanceController::class, 'index'])->name('index');
        Route::get('/mark',   [AttendanceController::class, 'create'])->name('create');
        Route::post('/mark',  [AttendanceController::class, 'store'])->name('store');
        Route::get('/edit',   [AttendanceController::class, 'edit'])->name('edit');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
    });

    /*
    |------------------------------------------------------------------
    | Fees & Billing Module
    |------------------------------------------------------------------
    */
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/',                  [FeeController::class, 'index'])->name('index');
        Route::get('/structures',        [FeeController::class, 'structures'])->name('structures');
        Route::post('/structures',       [FeeController::class, 'storeStructure'])->name('structures.store');
        Route::get('/create',            [FeeController::class, 'create'])->name('create');
        Route::post('/store',            [FeeController::class, 'store'])->name('store');
        Route::post('/invoices',         [FeeController::class, 'createInvoice'])->name('invoices.create');
        Route::get('/invoices/{invoice}', [FeeController::class, 'showInvoice'])->name('show-invoice');
        Route::get('/{fee}',             [FeeController::class, 'show'])->name('show');
        Route::get('/{fee}/edit',        [FeeController::class, 'edit'])->name('edit');
        Route::put('/{fee}',             [FeeController::class, 'update'])->name('update');
        Route::delete('/{fee}',          [FeeController::class, 'destroy'])->name('destroy');
        Route::get('/student/{student}', [FeeController::class, 'studentFees'])->name('student');
    });

    /*
    |------------------------------------------------------------------
    | Finance Officer Workspace
    |------------------------------------------------------------------
    */
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::get('/budget', [FinanceController::class, 'budget'])->name('budget');
        Route::get('/refunds', [FinanceController::class, 'refunds'])->name('refunds');
        Route::post('/payments/{payment}/refunds', [FinanceController::class, 'requestRefund'])->name('refunds.request');
        Route::post('/refunds/{refund}/approve', [FinanceController::class, 'approveRefund'])->name('refunds.approve');
        Route::post('/payments/{payment}/reconcile', [FinanceController::class, 'reconcilePayment'])->name('payments.reconcile');
        Route::get('/audit', [FinanceController::class, 'audit'])->name('audit');
    });

    /*
    |------------------------------------------------------------------
    | Examinations & Results Module
    |------------------------------------------------------------------
    */
    Route::get('/exams/results',                          [ExamController::class, 'results'])->name('exams.results');
    Route::get('/exams/{exam}/marks',                     [ExamController::class, 'marks'])->name('exams.marks');
    Route::post('/exams/{exam}/marks',                    [ExamController::class, 'saveMarks'])->name('exams.marks.save');
    Route::post('/exams/{exam}/submit-results',            [ExamController::class, 'submitResults'])->name('exams.submit-results');
    Route::get('/exams/{exam}/report-card/{student}',     [ExamController::class, 'reportCard'])->name('exams.report-card');
    Route::resource('exams', ExamController::class);

    /*
    |------------------------------------------------------------------
    | Reports Module & CSV Exports
    |------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance',        [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendanceCsv'])->name('attendance.export');
        Route::get('/fees',              [ReportController::class, 'fees'])->name('fees');
        Route::get('/fees/export',       [ReportController::class, 'exportFeesCsv'])->name('fees.export');
        Route::get('/academic',          [ReportController::class, 'academic'])->name('academic');
    });

    /*
    |------------------------------------------------------------------
    | Admin Notice Board Management
    |------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('notices', NoticeController::class);
        Route::resource('facilities', FacilityController::class)->except(['show']);
        Route::get('/audit-logs', [AdminAuditController::class, 'index'])->name('audit-logs.index');
        // Admin management for accreditations and scholarships
        Route::resource('accreditations', App\Http\Controllers\AccreditationController::class);
        Route::resource('scholarships', App\Http\Controllers\ScholarshipController::class);
        Route::post('/scholarships/{scholarship}/awards', [App\Http\Controllers\ScholarshipController::class, 'assign'])->name('scholarships.awards.store');
        Route::post('/scholarship-awards/{award}/approve', [App\Http\Controllers\ScholarshipController::class, 'approveAward'])->name('scholarship-awards.approve');
    });

    /*
    |------------------------------------------------------------------
    | School Settings Module
    |------------------------------------------------------------------
    */
    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
