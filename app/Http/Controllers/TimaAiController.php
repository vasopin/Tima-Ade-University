<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\Guardian;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TimaAiController extends Controller
{
    public function respond(Request $request)
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:500']]);
        $user = $request->user()->load('role');
        $message = Str::lower($validated['message']);
        
        // Intent-first routing: navigation > data query > help > general
        $action = $this->actionFor($user, $message);
        $response = $this->generateResponse($user, $message, $action);

        return response()->json([
            'role' => $user->role?->slug,
            'response' => $response,
            'action' => $action,
        ]);
    }
    
    private function generateResponse(User $user, string $message, ?array $action): string
    {
        if ($this->isRestrictedRequest($message)) {
            return "I can't access that with your current permissions.";
        }

        // Navigation/action commands must be handled before data or general responses.
        if ($action) {
            return $this->navigationResponse($action);
        }

        // Specific data queries return only the requested authorized information.
        $dataResponse = $this->dataQueryResponse($user, $message);
        if ($dataResponse) {
            return $dataResponse;
        }

        // Help requests
        if ($this->isHelpRequest($message)) {
            return $this->helpFor($user);
        }

        // General conversation - return role-appropriate fallback
        return $this->generalResponse($user);
    }
    
    private function isDataQuery(string $message): bool
    {
        return Str::contains($message, ['show', 'tell', 'list', 'what', 'which', 'how many', 'how much']);
    }
    
    private function generalResponse(User $user): string
    {
        if ($user->isParent()) {
            return 'I can help with your linked children and their academic information.';
        }
        if ($user->isStudent()) {
            return 'I can help with your courses, videos, materials, assignments, attendance, and profile.';
        }
        if ($user->isTeacher()) {
            return 'I can help with your classes, courses, materials, attendance, and profile.';
        }
        if ($user->isSuperAdmin()) {
            return 'I am your Super Admin assistant. I can check live university data, open any authorized management module, explain what needs attention, and help you work through the existing administration workflows.';
        }
        if ($user->isAdmin()) {
            return 'I can help you manage the university through the authorized administration modules, including users, students, teachers, parents, classes, subjects, fees, attendance, exams, reports, notices, settings, and your profile.';
        }
        return 'I can help you navigate the portal.';
    }
    
    private function navigationResponse(?array $action): string
    {
        if (!$action) return '';
        return 'Sure, opening ' . ($action['label'] ?? 'page') . '.';
    }
    
    private function dataQueryResponse(User $user, string $message): ?string
    {
        // Check for specific data queries - return only requested data
        
        if ($user->isStudent()) {
            // Student data queries
            $student = $user->student;
            if (Str::contains($message, ['assignment']) && Str::contains($message, ['what', 'which', 'show', 'have'])) {
                $assignments = $student?->schoolClass?->assignments()
                    ->where(function ($query) {
                        $query->whereNull('due_date')->orWhereDate('due_date', '>=', today());
                    })
                    ->orderBy('due_date')
                    ->pluck('title') ?? collect();
                return $assignments->isNotEmpty()
                    ? 'Your assignments are: ' . $assignments->join(', ') . '.'
                    : 'You have no current assignments.';
            }
            if (Str::contains($message, ['assignment']) && Str::contains($message, ['due', 'upcoming'])) {
                $assignments = $student?->schoolClass?->assignments()
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '>=', today())
                    ->orderBy('due_date')
                    ->pluck('title') ?? collect();
                return $assignments->isNotEmpty()
                    ? 'Assignments due: ' . $assignments->join(', ') . '.'
                    : 'You have no assignments due.';
            }
            if (Str::contains($message, ['how many course', 'how many subject'])) {
                $student = $user->student()->with(['schoolClass.subjects'])->first();
                $count = $student?->schoolClass?->subjects?->count() ?? 0;
                return "You have {$count} course(s).";
            }
            if (Str::contains($message, ['what', 'which', 'show']) && Str::contains($message, ['course', 'subject'])) {
                $student = $user->student()->with(['schoolClass.subjects'])->first();
                $subjects = $student?->schoolClass?->subjects?->pluck('name')->join(', ');
                return $subjects ? "Your courses are: {$subjects}." : 'No courses are currently assigned.';
            }
            if ($this->isAbsentCountQuery($message)) {
                $count = $student ? Attendance::where('student_id', $student->id)->whereDate('attendance_date', today())->where('status', 'absent')->count() : 0;
                return "You have {$count} absent record(s) today.";
            }
            if ($this->isAttendanceSummaryQuery($message)) {
                $records = $student?->attendances()->whereDate('attendance_date', today())->get() ?? collect();
                return 'Today: ' . $records->where('status', 'present')->count() . ' present, ' . $records->where('status', 'absent')->count() . ' absent, and ' . $records->where('status', 'late')->count() . ' late record(s).';
            }
            if (Str::contains($message, ['attendance']) && Str::contains($message, ['how', 'what', 'status'])) {
                $records = $student?->attendances()->whereDate('attendance_date', '>=', now()->subDays(30))->get() ?? collect();
                $present = $records->where('status', 'present')->count();
                $absent = $records->where('status', 'absent')->count();
                return "In the last 30 days, you have {$present} present and {$absent} absent record(s).";
            }
            if (Str::contains($message, ['how many assignment', 'how many assignment due'])) {
                $count = $student?->schoolClass?->assignments()->count() ?? 0;
                return "You have {$count} assignment(s).";
            }
            if (Str::contains($message, ['fee', 'ledger']) && Str::contains($message, ['how much', 'what', 'balance'])) {
                $amount = $student?->feePayments()->where('status', 'paid')->sum('amount_paid') ?? 0;
                return 'Your recorded paid fees total ' . number_format((float) $amount, 2) . '.';
            }
        }
        
        if ($user->isParent()) {
            // Parent data queries
            if (Str::contains($message, ['show', 'tell', 'list', 'what']) && Str::contains($message, ['child', 'children'])) {
                $children = $user->guardian?->students()->with('user')->get() ?? collect();
                if ($children->isEmpty()) {
                    return 'No students are currently linked to your parent account.';
                }
                $names = $children->map(fn ($child) => $child->user?->name)->filter()->join(', ');
                return "Your linked children are {$names}.";
            }
            if (!$this->isAbsentCountQuery($message) && Str::contains($message, ['how many child', 'how many student'])) {
                $children = $user->guardian?->students()->count() ?? 0;
                return "You have {$children} linked child(ren).";
            }
            if ($this->isAbsentCountQuery($message)) {
                $children = $user->guardian?->students()->get() ?? collect();
                $ids = $children->pluck('id');
                $count = Attendance::whereIn('student_id', $ids)->whereDate('attendance_date', today())->where('status', 'absent')->count();
                return "There are {$count} absent record(s) today among your children.";
            }
            if ($this->isAttendanceSummaryQuery($message)) {
                $ids = ($user->guardian?->students()->pluck('students.id')) ?? collect();
                $records = Attendance::whereIn('student_id', $ids)->whereDate('attendance_date', today())->get();
                return 'Today among your children: ' . $records->where('status', 'present')->count() . ' present, ' . $records->where('status', 'absent')->count() . ' absent, and ' . $records->where('status', 'late')->count() . ' late record(s).';
            }
        }
        
        if ($user->isTeacher()) {
            // Teacher data queries
            if (!$this->isAbsentCountQuery($message) && Str::contains($message, ['how many student', 'how many class'])) {
                $classIds = SchoolClass::whereHas('subjects', fn ($query) => $query->where('teacher_id', $user->id))->pluck('id');
                $count = Student::whereIn('school_class_id', $classIds)->count();
                return "You have {$count} student(s) in your assigned classes.";
            }
            if ($this->isAbsentCountQuery($message)) {
                $classIds = SchoolClass::whereHas('subjects', fn ($query) => $query->where('teacher_id', $user->id))->pluck('id');
                $count = Attendance::whereIn('school_class_id', $classIds)->whereDate('attendance_date', today())->where('status', 'absent')->count();
                return "There are {$count} absent record(s) today in your classes.";
            }
            if ($this->isAttendanceSummaryQuery($message)) {
                $classIds = SchoolClass::whereHas('subjects', fn ($query) => $query->where('teacher_id', $user->id))->pluck('id');
                $records = Attendance::whereIn('school_class_id', $classIds)->whereDate('attendance_date', today())->get();
                return 'Today in your classes: ' . $records->where('status', 'present')->count() . ' present, ' . $records->where('status', 'absent')->count() . ' absent, and ' . $records->where('status', 'late')->count() . ' late record(s).';
            }
            if (Str::contains($message, ['exam', 'examination']) && Str::contains($message, ['what', 'which', 'show', 'scheduled', 'upcoming'])) {
                $classIds = SchoolClass::whereHas('subjects', fn ($query) => $query->where('teacher_id', $user->id))->pluck('id');
                $names = Exam::whereIn('school_class_id', $classIds)->whereIn('status', ['scheduled', 'ongoing'])->orderBy('exam_date')->pluck('name');
                return $names->isNotEmpty() ? 'Your scheduled examinations are: ' . $names->join(', ') . '.' : 'There are no scheduled examinations for your classes.';
            }
        }
        
        if ($user->isAdmin()) {
            // Admin data queries
            if (Str::contains($message, ['university statistics', 'school statistics'])) {
                return 'There are ' . Student::count() . ' student(s) and ' . User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->count() . ' teacher(s) in the university records.';
            }
            if ($this->isDashboardSummaryQuery($message)) {
                $absent = Attendance::whereDate('attendance_date', today())->where('status', 'absent')->count();
                $collected = FeePayment::where('status', 'paid')->sum('amount_paid');
                $exams = Exam::whereDate('exam_date', today())->whereIn('status', ['scheduled', 'ongoing'])->count();
                $notices = Notice::where('is_active', true)->whereDate('published_date', '<=', today())->count();
                return 'Today: ' . Student::count() . ' students, ' . User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->where('is_active', true)->count() . ' active teachers, ' . $absent . ' absent student record(s), ' . number_format((float) $collected, 2) . ' collected, ' . $exams . ' exam(s), and ' . $notices . ' active notice(s).';
            }
            if (Str::contains($message, ['how many user', 'number of user', 'registered user'])) {
                return 'There are ' . User::count() . ' registered user(s).';
            }
            if (Str::contains($message, ['how many parent', 'number of parent'])) {
                return 'There are ' . Guardian::count() . ' registered parent account(s).';
            }
            if (Str::contains($message, ['how many section', 'number of section'])) {
                return 'There are ' . Section::count() . ' section(s).';
            }
            if (Str::contains($message, ['how many teacher']) && Str::contains($message, ['active', 'working'])) {
                $count = User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))
                    ->where('is_active', true)->count();
                return 'There are ' . $count . ' active teacher(s).';
            }
            if (Str::contains($message, ['which student', 'students']) && Str::contains($message, ['absent', 'missing'])) {
                $students = Student::with('user')->whereHas('attendances', fn ($query) => $query
                    ->whereDate('attendance_date', today())->where('status', 'absent'))->get();
                return $students->isNotEmpty()
                    ? 'Students marked absent today: ' . $students->map(fn ($student) => $student->user?->name)->filter()->join(', ') . '.'
                    : 'No students are marked absent today.';
            }
            if (Str::contains($message, ['more than four', 'over four', 'repeated', 'poor attendance'])) {
                $students = Student::with('user')->whereHas('attendances', fn ($query) => $query
                    ->where('status', 'absent'))->get()->filter(fn ($student) => $student->attendances()
                    ->where('status', 'absent')->count() > 4);
                return $students->isNotEmpty()
                    ? 'Students with repeated absences: ' . $students->map(fn ($student) => $student->user?->name)->filter()->join(', ') . '.'
                    : 'No students have more than four absence records.';
            }
            if (Str::contains($message, ['teacher']) && Str::contains($message, ['absent', 'missing'])) {
                return 'Teacher attendance is not recorded in the current attendance data.';
            }
            if (!$this->isAbsentCountQuery($message) && Str::contains($message, ['how many student'])) {
                $count = Student::count();
                return "There are {$count} student(s) in the system.";
            }
            if (Str::contains($message, ['how many teacher'])) {
                $count = User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->count();
                return "There are {$count} teacher(s) in the system.";
            }
            if (Str::contains($message, ['how much', 'amount', 'fee']) && Str::contains($message, ['collected', 'paid', 'received'])) {
                $amount = FeePayment::where('status', 'paid')->sum('amount_paid');
                return 'The total fee collected is ' . number_format((float) $amount, 2) . '.';
            }
            if (Str::contains($message, ['outstanding', 'unpaid', 'overdue'])) {
                $payments = FeePayment::with('feeStructure')->whereIn('status', ['pending', 'partial', 'overdue'])->get();
                $amount = $payments->sum(fn ($payment) => max(0, (float) $payment->total_due - (float) $payment->amount_paid));
                return 'The recorded outstanding balance is ' . number_format($amount, 2) . '.';
            }
            if (Str::contains($message, ['exam', 'examination']) && Str::contains($message, ['scheduled', 'upcoming', 'what', 'which', 'show'])) {
                $exams = Exam::with(['schoolClass', 'subject'])
                    ->whereIn('status', ['scheduled', 'ongoing'])
                    ->orderBy('exam_date')
                    ->limit(10)
                    ->get();
                if ($exams->isEmpty()) return 'There are no scheduled examinations.';
                $names = $exams->map(fn ($exam) => $exam->name . ' on ' . $exam->exam_date?->format('M j'));
                return 'Scheduled examinations: ' . $names->join(', ') . '.';
            }
            if ($this->isAbsentCountQuery($message)) {
                $count = Attendance::whereDate('attendance_date', today())->where('status', 'absent')->count();
                return "There are {$count} absent record(s) today.";
            }
            if ($this->isAttendanceSummaryQuery($message)) {
                $records = Attendance::whereDate('attendance_date', today())->get();
                return 'Today: ' . $records->where('status', 'present')->count() . ' present, ' . $records->where('status', 'absent')->count() . ' absent, and ' . $records->where('status', 'late')->count() . ' late record(s).';
            }
            if (Str::contains($message, ['video', 'videos']) && Str::contains($message, ['latest', 'new', 'what', 'show', 'graduation', 'event'])) {
                $videos = Notice::where('is_active', true)->where('status', 'published')
                    ->whereDate('published_date', '<=', today())
                    ->where(function ($query) { $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()); })
                    ->where(function ($query) { $query->whereNotNull('video_path')->orWhereNotNull('external_video_url'); })
                    ->latest('published_date')->limit(5)->get(['title', 'category']);
                return $videos->isNotEmpty()
                    ? 'Latest university videos: ' . $videos->map(fn ($video) => $video->title . ' (' . $video->category . ')')->join(', ') . '.'
                    : 'There are no current university videos.';
            }
            if (Str::contains($message, ['notice', 'announcement']) && Str::contains($message, ['latest', 'new', 'what', 'show'])) {
                $notices = Notice::where('is_active', true)->whereDate('published_date', '<=', today())
                    ->latest('published_date')->limit(5)->pluck('title');
                return $notices->isNotEmpty() ? 'Latest notices: ' . $notices->join(', ') . '.' : 'There are no current notices.';
            }
            if (Str::contains($message, ['timetable', 'schedule']) && Str::contains($message, ['today', 'scheduled', 'what'])) {
                $entries = Timetable::with(['schoolClass', 'subject'])->where('is_active', true)
                    ->where('day_of_week', now()->format('l'))->orderBy('start_time')->get();
                return $entries->isNotEmpty()
                    ? "Today's timetable includes: " . $entries->map(fn ($entry) => ($entry->subject?->name ?? 'Subject') . ' for ' . ($entry->schoolClass?->name ?? 'class'))->join(', ') . '.'
                    : "There are no timetable entries for today.";
            }
        }

        if (Str::contains($message, ['academic matrix']) && Str::contains($message, ['what', 'how'])) {
            return 'The academic matrix summarizes academic performance by class, subject, and student.';
        }
        
        return null;
    }

    private function isAbsentCountQuery(string $message): bool
    {
        return Str::contains($message, 'absent')
            && Str::contains($message, ['how many', 'number of', 'count']);
    }

    private function isAttendanceSummaryQuery(string $message): bool
    {
        return Str::contains($message, 'attendance')
            && Str::contains($message, ['today', 'summary', 'status'])
            && !$this->isAbsentCountQuery($message);
    }

    private function isDashboardSummaryQuery(string $message): bool
    {
        return Str::contains($message, ['what is happening', 'school summary', 'important information', 'what needs my attention', 'give me a summary'])
            || (Str::contains($message, ['summary', 'happening', 'important']) && Str::contains($message, 'today'));
    }

    private function isNoticeQuery(string $message): bool
    {
        return Str::contains($message, ['notice', 'announcement'])
            && Str::contains($message, ['latest', 'new', 'what', 'show'])
            && !Str::contains($message, ['open', 'go to', 'navigate']);
    }
    
    private function isHelpRequest(string $message): bool
    {
        return Str::contains($message, ['help', 'what can', 'what can you do', 'how can you help', 'where']);
    }

    private function isRestrictedRequest(string $message): bool
    {
        return Str::contains($message, ['another student', 'other student', 'all teachers', 'teacher private', 'private information', 'another family']);
    }

    private function actionFor(User $user, string $message): ?array
    {
        if ($this->isRestrictedRequest($message)) return null;
        if ($this->isAttendanceSummaryQuery($message)) return null;
        if ($this->isDashboardSummaryQuery($message)) return null;
        if ($user->isAdmin() && $this->isNoticeQuery($message)) return null;

        if ($user->isParent()
            && Str::contains($message, ['child', 'children'])
            && Str::contains($message, 'show')
            && !Str::contains($message, ['dashboard', 'open'])) {
            return null;
        }

        if (!Str::contains($message, [
            'open',
            'show',
            'take me',
            'go to',
            'navigate to',
            'display',
        ])) {
            return null;
        }

        $route = null;
        $label = null;

        // Match action keywords first
        if ($user->isParent()) {
            if (Str::contains($message, ['child', 'children', 'my children', 'open.*child'])) {
                $route = 'parent.children';
                $label = 'My Children';
            } elseif (Str::contains($message, ['profile'])) {
                $route = 'profile.show';
                $label = 'Profile';
            } elseif (Str::contains($message, ['report']) && Str::contains($message, ['attendance'])) {
                $route = 'attendance.report';
                $label = 'Attendance Report';
            } elseif (Str::contains($message, ['attendance', 'open.*attendance'])) {
                $route = 'attendance.index';
                $label = 'Attendance';
            } elseif (Str::contains($message, ['result', 'grade', 'exam', 'open.*result'])) {
                $route = 'exams.results';
                $label = 'Academic Results';
            } elseif (Str::contains($message, ['announcement', 'notice', 'open.*announcement'])) {
                $route = 'public.notices';
                $label = 'Announcements';
            }
        } elseif ($user->isStudent()) {
            if (Str::contains($message, ['course', 'subject', 'class', 'open.*course', 'open.*subject'])) {
                $route = 'student.courses';
                $label = 'My Courses';
            } elseif (Str::contains($message, ['assignment', 'open.*assignment'])) {
                $route = 'student.assignments';
                $label = 'Assignments';
            } elseif (Str::contains($message, ['attendance', 'open.*attendance'])) {
                $route = 'student.attendance';
                $label = 'Attendance';
            } elseif (Str::contains($message, ['announcement', 'notice', 'open.*announcement'])) {
                $route = 'student.announcements';
                $label = 'Announcements';
            } elseif (Str::contains($message, ['fee ledger', 'fees', 'fee'])) {
                $route = 'fees.student';
                $label = 'My Fee Ledger';
            } elseif (Str::contains($message, ['video', 'open.*video', 'my video'])) {
                $route = 'student.videos';
                $label = 'Videos';
            } elseif (Str::contains($message, ['material', 'resource', 'open.*material', 'open.*resource'])) {
                $route = 'student.materials';
                $label = 'Materials';
            } elseif (Str::contains($message, ['profile', 'open.*profile'])) {
                $route = 'profile.show';
                $label = 'Profile';
            } elseif (Str::contains($message, ['dashboard', 'open.*dashboard'])) {
                $route = 'student.dashboard';
                $label = 'Dashboard';
            }
        } elseif ($user->isTeacher()) {
            if (Str::contains($message, ['student', 'class', 'my class', 'open.*class', 'show.*student'])) {
                $route = 'teacher.classes';
                $label = 'My Classes';
            } elseif (Str::contains($message, ['course', 'open.*course'])) {
                $route = 'teacher.courses';
                $label = 'My Courses';
            } elseif (Str::contains($message, ['video', 'open.*video'])) {
                $route = 'teacher.videos';
                $label = 'Videos';
            } elseif (Str::contains($message, ['material', 'resource', 'open.*material', 'open.*resource'])) {
                $route = 'teacher.materials';
                $label = 'Materials';
            } elseif (Str::contains($message, ['report']) && Str::contains($message, ['attendance'])) {
                $route = 'attendance.report';
                $label = 'Attendance Report';
            } elseif (Str::contains($message, ['attendance', 'open.*attendance'])) {
                $route = 'teacher.attendance';
                $label = 'Attendance';
            } elseif (Str::contains($message, ['academic matrix'])) {
                $route = 'reports.academic';
                $label = 'Academic Matrix';
            } elseif (Str::contains($message, ['exam', 'examination'])) {
                $route = 'exams.index';
                $label = 'Examinations';
            } elseif (Str::contains($message, ['notice', 'announcement'])) {
                $route = 'public.notices';
                $label = 'Notices';
            } elseif (Str::contains($message, ['profile', 'open.*profile'])) {
                $route = 'profile.show';
                $label = 'Profile';
            } elseif (Str::contains($message, ['dashboard', 'open.*dashboard'])) {
                $route = 'teacher.dashboard';
                $label = 'Dashboard';
            }
        } elseif ($user->isAdmin()) {
            foreach ($this->superAdminModuleRegistry() as $module) {
                if (Str::contains($message, $module['keywords'])) {
                    $route = $module['route'];
                    $label = $module['label'];
                    break;
                }
            }

            if ($route) {
                return ['type' => 'navigate', 'url' => route($route), 'label' => $label];
            }

            if (Str::contains($message, ['dashboard'])) {
                $route = 'dashboard';
                $label = 'Dashboard';
            } elseif (Str::contains($message, ['user account', 'user management'])) {
                $route = 'users.index';
                $label = 'User Accounts';
            } elseif (Str::contains($message, ['parent', 'guardian'])) {
                $route = 'parents.index';
                $label = 'Parents and Guardians';
            } elseif (Str::contains($message, ['student', 'enrollment', 'open.*student', 'show.*student'])) {
                $route = 'students.index';
                $label = 'Students';
            } elseif (Str::contains($message, ['teacher', 'open.*teacher', 'show.*teacher'])) {
                $route = 'teachers.index';
                $label = 'Teachers';
            } elseif (Str::contains($message, ['class', 'section', 'open.*class'])) {
                $route = 'classes.index';
                $label = 'Classes';
            } elseif (Str::contains($message, ['course', 'subject', 'open.*course', 'open.*subject'])) {
                $route = 'subjects.index';
                $label = 'Courses';
            } elseif (Str::contains($message, ['attendance report'])) {
                $route = 'reports.attendance';
                $label = 'Attendance Report';
            } elseif (Str::contains($message, ['attendance', 'open.*attendance'])) {
                $route = 'attendance.index';
                $label = 'Attendance';
            } elseif (Str::contains($message, ['fee collection report', 'fee report'])) {
                $route = 'reports.fees';
                $label = 'Fee Collection Report';
            } elseif (Str::contains($message, ['fee structure'])) {
                $route = 'fees.structures';
                $label = 'Fee Structure';
            } elseif (Str::contains($message, ['fee collection', 'fees'])) {
                $route = 'fees.index';
                $label = 'Fee Collection';
            } elseif (Str::contains($message, ['academic matrix'])) {
                $route = 'reports.academic';
                $label = 'Academic Matrix';
            } elseif (Str::contains($message, ['timetable', 'schedule'])) {
                $route = 'timetables.index';
                $label = 'Timetable';
            } elseif (Str::contains($message, ['examination', 'exam'])) {
                $route = 'exams.index';
                $label = 'Examinations';
            } elseif (Str::contains($message, ['school setting', 'settings'])) {
                $route = 'settings.index';
                $label = 'School Settings';
            } elseif (Str::contains($message, ['notice board', 'notice', 'announcement'])) {
                $route = 'admin.notices.index';
                $label = 'Notice Board';
            } elseif (Str::contains($message, ['profile'])) {
                $route = 'profile.show';
                $label = 'Profile';
            }
        }

        if (!$route) return null;

        if ($route === 'fees.student') {
            if (!$user->student) return null;
            return ['type' => 'navigate', 'url' => route($route, $user->student), 'label' => $label];
        }

        return ['type' => 'navigate', 'url' => route($route), 'label' => $label];
    }


    private function helpFor(User $user): string
    {
        return match (true) {
            $user->isParent() => 'I can help with your linked children and their academic information. Try asking me to open My Children, or ask about attendance and results.',
            $user->isStudent() => 'I can help with your courses, videos, materials, assignments, attendance, and profile. Try asking me to open one of these pages.',
            $user->isTeacher() => 'I can help with your classes, courses, materials, attendance, and profile. Try asking me to open one of these pages.',
            $user->isSuperAdmin() => 'I am your Super Admin assistant. I can work across every authorized dashboard module: users, students, teachers, parents, classes, subjects, timetable, attendance, examinations, fees, reports, notices, settings, profile, and security.',
            $user->isAdmin() => 'I can help across your full administration workspace, including users, students, teachers, parents, classes, subjects, timetable, attendance, examinations, fees, reports, notices, settings, and your profile.',
            default => 'I can help you navigate the university portal. Ask me to open a page or answer a specific question.',
        };
    }

    private function superAdminModuleRegistry(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'keywords' => ['dashboard']],
            ['label' => 'User Accounts', 'route' => 'users.index', 'keywords' => ['user account', 'user management', 'users']],
            ['label' => 'Students', 'route' => 'students.index', 'keywords' => ['students', 'student management']],
            ['label' => 'Teachers', 'route' => 'teachers.index', 'keywords' => ['teachers', 'teacher management']],
            ['label' => 'Parents and Guardians', 'route' => 'parents.index', 'keywords' => ['parents', 'guardians']],
            ['label' => 'Classes and Sections', 'route' => 'classes.index', 'keywords' => ['classes', 'sections']],
            ['label' => 'Subjects', 'route' => 'subjects.index', 'keywords' => ['subjects']],
            ['label' => 'Timetable', 'route' => 'timetables.index', 'keywords' => ['timetable', 'schedule']],
            ['label' => 'Attendance Report', 'route' => 'reports.attendance', 'keywords' => ['attendance report', 'attendance reports']],
            ['label' => 'Attendance Register', 'route' => 'attendance.index', 'keywords' => ['daily attendance', 'attendance register']],
            ['label' => 'Examinations', 'route' => 'exams.index', 'keywords' => ['examinations', 'examination']],
            ['label' => 'Fee Collection Report', 'route' => 'reports.fees', 'keywords' => ['fee collection report', 'fee collection reports']],
            ['label' => 'Fee Collection', 'route' => 'fees.index', 'keywords' => ['fee collection']],
            ['label' => 'Fee Structure', 'route' => 'fees.structures', 'keywords' => ['fee structure']],
            ['label' => 'Academic Matrix', 'route' => 'reports.academic', 'keywords' => ['academic matrix']],
            ['label' => 'Notice Board', 'route' => 'admin.notices.index', 'keywords' => ['notice board']],
            ['label' => 'Notices', 'route' => 'admin.notices.index', 'keywords' => ['notices']],
            ['label' => 'School Settings', 'route' => 'settings.index', 'keywords' => ['school settings']],
            ['label' => 'Security and Profile', 'route' => 'profile.show', 'keywords' => ['security', 'my profile', 'profile']],
        ];
    }
}
