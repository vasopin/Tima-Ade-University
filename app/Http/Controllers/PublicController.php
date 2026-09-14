<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Notice;
use App\Models\Event;
use App\Models\Testimonial;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    /**
     * Homepage with hero, stats, faculty highlights, notices, and academic programs.
     */
    public function home()
    {
        try {
            $stats = [
                'students_count' => Student::where('status', 'active')->count(),
                'teachers_count' => Teacher::count(),
                'classes_count'  => SchoolClass::where('is_active', true)->count(),
                'subjects_count' => Subject::where('is_active', true)->count(),
            ];

            $classes = SchoolClass::with(['subjects', 'sections'])
                ->where('is_active', true)
                ->take(4)
                ->get();

            $teachers = Teacher::with('user')
                ->latest()
                ->take(4)
                ->get();

            $notices = Notice::where('is_active', true)
                ->where('status', 'published')
                ->whereDate('published_date', '<=', today())
                ->where(function ($query) { $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()); })
                ->where('audience', 'everyone')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('published_date', 'desc')
                ->take(3)
                ->get();

            $events = Event::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhere('starts_at', '>=', now());
                })
                ->orderBy('starts_at')
                ->take(3)
                ->get();

            $testimonials = Testimonial::where('is_active', true)
                ->latest()
                ->take(3)
                ->get();
        } catch (\Throwable $e) {
            // Log the DB/driver error and fall back to safe defaults so the public site can load.
            Log::error('PublicController::home DB error: ' . $e->getMessage());

            $stats = [
                'students_count' => null,
                'teachers_count' => null,
                'classes_count'  => null,
                'subjects_count' => null,
            ];
            $classes = collect();
            $teachers = collect();
            $notices = collect();
            // Ensure these are always defined for the view
            $events = collect();
            $testimonials = collect();
        }

        return view('public.home', compact('stats', 'classes', 'teachers', 'notices', 'events', 'testimonials'));
    }

    /**
     * About Us page with history, vision, mission, and accreditation.
     */
    public function about()
    {
        try {
            $teachersCount = Teacher::count();
            $studentsCount = Student::where('status', 'active')->count();
        } catch (\Throwable $e) {
            Log::error('PublicController::about DB error: ' . $e->getMessage());
            $teachersCount = null;
            $studentsCount = null;
        }

        $aboutImages = config('about.images');

        return view('public.about', compact('teachersCount', 'studentsCount', 'aboutImages'));
    }

    public function aboutBoard()
    {
        return view('public.about-board', ['aboutImages' => config('about.images')]);
    }

    public function aboutCampuses()
    {
        return view('public.about-campuses', ['aboutImages' => config('about.images')]);
    }

    /**
     * Academics / Programs overview.
     */
    public function academics()
    {
        try {
            $classes = SchoolClass::with(['subjects', 'sections', 'classTeacher.user'])
                ->where('is_active', true)
                ->get();

            $subjects = Subject::withCount('classes')
                ->where('is_active', true)
                ->get();
        } catch (\Throwable $e) {
            Log::error('PublicController::academics DB error: ' . $e->getMessage());
            $classes = collect();
            $subjects = collect();
        }

        return view('public.academics', compact('classes', 'subjects'));
    }

    public function programs()
    {
        return $this->academics();
    }

    public function programCalendar()
    {
        return view('public.program-calendar', ['programImages' => config('programs.images')]);
    }

    public function programCareers()
    {
        return view('public.program-careers', ['programImages' => config('programs.images')]);
    }

    /**
     * Campus Facilities overview page.
     */
    public function facilities()
    {
        try {
            // Use static, structured content for facilities so the page is complete without requiring new models immediately.
            $facilities = collect([
                [
                    'title' => 'Central Library',
                    'icon' => 'bi-bookshelf',
                    'description' => 'A multi-level library with print collections, electronic journals, group study rooms, and archival resources supporting research and coursework.',
                    'image' => '/images/facilities/library.jpg'
                ],
                [
                    'title' => 'Science & Research Labs',
                    'icon' => 'bi-radioactive',
                    'description' => 'State-of-the-art physics, chemistry, and biology laboratories with safety-trained technicians and modern instruments for hands-on experiments.',
                    'image' => '/images/home/lab.jpg'
                ],
                [
                    'title' => 'Computer & AI Labs',
                    'icon' => 'bi-cpu-fill',
                    'description' => 'High-performance computing clusters, AI/ML workstations, and collaborative maker spaces to support computing education and research.',
                    'image' => '/images/home/tech.jpg'
                ],
                [
                    'title' => 'Student Housing',
                    'icon' => 'bi-house-fill',
                    'description' => 'Secure residential halls with furnished rooms, communal kitchens, study lounges, and resident advisors to support student life on campus.',
                    'image' => '/images/home/campus.jpg'
                ],
                [
                    'title' => 'Sports Complex',
                    'icon' => 'bi-trophy',
                    'description' => 'Indoor gymnasium, athletics track, swimming pool, and courts for basketball, tennis, and other recreational sports.',
                    'image' => '/images/home/event.jpg'
                ],
                [
                    'title' => 'Health & Counseling Center',
                    'icon' => 'bi-heart-pulse',
                    'description' => 'On-campus medical clinic and counseling services offering primary care, wellness workshops, and mental health support.',
                    'image' => '/images/home/community.jpg'
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('PublicController::facilities DB error: ' . $e->getMessage());
            $facilities = collect();
        }

        return view('public.facilities', compact('facilities'));
    }

    private function eCampusInfo(string $page)
    {
        return view('public.e-campus-info', ['page' => $page]);
    }

    public function eCampusLearning() { return $this->eCampusInfo('learning'); }
    public function eCampusResources() { return $this->eCampusInfo('resources'); }
    public function eCampusStudentServices() { return $this->eCampusInfo('student-services'); }
    public function eCampusHelp() { return $this->eCampusInfo('help'); }

    /**
     * Public Faculty directory with search & filter.
     */
    public function faculty(Request $request)
    {
        try {
            $query = Teacher::with(['user', 'classTeacherOf']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhere('specialization', 'like', "%$search%")
                      ->orWhere('qualification', 'like', "%$search%");
            }

            $teachers = $query->paginate(12)->withQueryString();
        } catch (\Throwable $e) {
            Log::error('PublicController::faculty DB error: ' . $e->getMessage());
            $teachers = collect();
        }

        return view('public.faculty', compact('teachers'));
    }

    /**
     * Admissions information & application form.
     */
    public function admissions()
    {
        try {
            $classes = SchoolClass::where('is_active', true)->get();
        } catch (\Throwable $e) {
            Log::error('PublicController::admissions DB error: ' . $e->getMessage());
            $classes = collect();
        }

        return view('public.admissions', compact('classes'));
    }

    private function admissionsInfo(string $page)
    {
        return view('public.admissions-info', [
            'page' => $page,
            'admissionsImages' => config('admissions.images'),
        ]);
    }

    public function admissionsHowToApply() { return $this->admissionsInfo('how-to-apply'); }
    public function admissionsRequirements() { return $this->admissionsInfo('requirements'); }
    public function admissionsProcess() { return $this->admissionsInfo('application-process'); }
    public function admissionsDates() { return $this->admissionsInfo('dates'); }
    public function admissionsFees() { return $this->admissionsInfo('fees'); }
    public function admissionsInternational() { return $this->admissionsInfo('international'); }
    public function admissionsFaq() { return $this->admissionsInfo('faq'); }


    /**
     * Handle online admission inquiry / application.
     */
    public function storeAdmission(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:20',
            'grade_interested' => 'required|string|max:100',
            'message'          => 'nullable|string|max:1000',
        ]);

        Inquiry::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'subject'          => 'Admission Application for ' . $validated['grade_interested'],
            'type'             => 'admission',
            'grade_interested' => $validated['grade_interested'],
            'message'          => $validated['message'] ?? 'Admission inquiry submitted online.',
            'status'           => 'new',
        ]);

        return redirect()->route('public.admissions')
            ->with('success', 'Thank you! Your admission inquiry for ' . $validated['name'] . ' has been received. Our admissions registrar will contact you shortly.');
    }

    /**
     * Contact Us page.
     */
    public function contact()
    {
        return view('public.contact');
    }

    public function hemis()
    {
        $this->authorizeAdministrationPortal();

        return view('hemis.index');
    }

    public function hemisLookup(Request $request)
    {
        $this->authorizeAdministrationPortal();

        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:100'],
        ]);

        $student = Student::with(['user', 'schoolClass', 'section'])
            ->where(function ($query) use ($validated) {
                $query->where('student_id', $validated['student_id'])
                    ->orWhere('admission_number', $validated['student_id'])
                    ->orWhere('roll_number', $validated['student_id']);
            })
            ->first();

        return view('hemis.index', [
            'student' => $student,
            'searchedId' => $validated['student_id'],
        ]);
    }

    private function authorizeAdministrationPortal(): void
    {
        $user = auth()->user();

        if ($user?->isAdmin() || $user?->isStaff()) {
            return;
        }

        if ($user) {
            abort(response()->view('errors.portal-access', [
                'dashboardRoute' => $user->isTeacher() ? 'teacher.classes' : 'dashboard',
            ], 403));
        }

        abort(403, 'Administrative portal access required.');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $pages = [
            ['title' => 'Tima-Ade University', 'category' => 'Home', 'excerpt' => 'Explore the university, its academic environment, community, and public information.', 'route' => 'public.home', 'terms' => 'home university campus education'],
            ['title' => 'About Tima-Ade', 'category' => 'About', 'excerpt' => 'Learn more about Tima-Ade University and the information published about its community.', 'route' => 'public.about', 'terms' => 'about history mission vision university'],
            ['title' => 'Board of Directors', 'category' => 'About', 'excerpt' => 'Meet the leadership information published for the university community.', 'route' => 'public.about.board', 'terms' => 'board directors leadership governance'],
            ['title' => 'Campuses', 'category' => 'About', 'excerpt' => 'Explore the campus information available from Tima-Ade University.', 'route' => 'public.about.campuses', 'terms' => 'campus campuses location'],
            ['title' => 'Academic Programs', 'category' => 'Programs', 'excerpt' => 'Review the academic programs and subjects currently available in the public catalogue.', 'route' => 'public.programs', 'terms' => 'program programs academic courses subjects'],
            ['title' => 'Academic Calendar', 'category' => 'Programs', 'excerpt' => 'Find the published academic calendar information and related planning resources.', 'route' => 'public.programs.calendar', 'terms' => 'calendar academic dates schedule'],
            ['title' => 'Careers & Certifications', 'category' => 'Programs', 'excerpt' => 'Explore career and certification information connected to academic study.', 'route' => 'public.programs.careers', 'terms' => 'careers certifications employment professional'],
            ['title' => 'Admissions', 'category' => 'Admissions', 'excerpt' => 'Begin your academic journey with admissions guidance, application access, and official information.', 'route' => 'public.admissions', 'terms' => 'admissions admission apply application university'],
            ['title' => 'How to Apply', 'category' => 'Admissions', 'excerpt' => 'Find a clear place to begin application planning and review the available guidance.', 'route' => 'public.admissions.how-to-apply', 'terms' => 'how apply application admissions'],
            ['title' => 'Admission Requirements', 'category' => 'Admissions', 'excerpt' => 'Review the structured requirements information and identify details that need confirmation.', 'route' => 'public.admissions.requirements', 'terms' => 'requirements admission documents eligibility'],
            ['title' => 'Application Process', 'category' => 'Admissions', 'excerpt' => 'Understand the planning framework around preparing, submitting, and reviewing an application.', 'route' => 'public.admissions.process', 'terms' => 'application process submit review admissions'],
            ['title' => 'Important Dates', 'category' => 'Admissions', 'excerpt' => 'Find the place for application periods, decisions, enrollment milestones, and notices.', 'route' => 'public.admissions.dates', 'terms' => 'dates deadlines calendar admissions'],
            ['title' => 'Fees & Funding', 'category' => 'Admissions', 'excerpt' => 'Find verified financial information when it is published by the university.', 'route' => 'public.admissions.fees', 'terms' => 'fees funding tuition scholarship financial'],
            ['title' => 'International Admissions', 'category' => 'Admissions', 'excerpt' => 'Find guidance for applicants outside the local context as it is officially confirmed.', 'route' => 'public.admissions.international', 'terms' => 'international admissions visa documentation global'],
            ['title' => 'Frequently Asked Questions', 'category' => 'Admissions', 'excerpt' => 'Browse common admissions questions and find the official answers available.', 'route' => 'public.admissions.faq', 'terms' => 'faq frequently questions answers admissions'],
            ['title' => 'E-Campus', 'category' => 'E-Campus', 'excerpt' => 'Enter the digital campus, explore facilities, and find public routes to university services.', 'route' => 'public.facilities', 'terms' => 'e-campus ecampus digital campus facilities services'],
            ['title' => 'Learning & Courses', 'category' => 'E-Campus', 'excerpt' => 'Find the public entry point to learning tools and course access.', 'route' => 'public.e-campus.learning', 'terms' => 'learning courses materials assignments'],
            ['title' => 'Academic Resources', 'category' => 'E-Campus', 'excerpt' => 'Explore the structured home for approved academic resources as they are published.', 'route' => 'public.e-campus.resources', 'terms' => 'academic resources library research materials'],
            ['title' => 'Student Services', 'category' => 'E-Campus', 'excerpt' => 'Find public guidance for the student-facing services represented in the application.', 'route' => 'public.e-campus.student-services', 'terms' => 'student services attendance fees notices records'],
            ['title' => 'Help & Support', 'category' => 'E-Campus', 'excerpt' => 'Find the right support channel for questions about the digital campus.', 'route' => 'public.e-campus.help', 'terms' => 'help support contact portal'],
            ['title' => 'Student Life', 'category' => 'Student Life', 'excerpt' => 'Explore student life, community, and public university activities.', 'route' => 'public.student-life', 'terms' => 'student life community activities'],
            ['title' => 'Alumni', 'category' => 'Student Life', 'excerpt' => 'Find the published alumni information and community links.', 'route' => 'public.student-life.alumni', 'terms' => 'alumni graduates community'],
            ['title' => 'Research', 'category' => 'Student Life', 'excerpt' => 'Explore the public research information available from the university.', 'route' => 'public.student-life.research', 'terms' => 'research innovation academic'],
            ['title' => 'News & Notices', 'category' => 'Student Life', 'excerpt' => 'Read current public notices, announcements, and university news.', 'route' => 'public.notices', 'terms' => 'news notices announcements updates'],
            ['title' => 'Contact', 'category' => 'Contact', 'excerpt' => 'Find the university contact channel for questions and enquiries.', 'route' => 'public.contact', 'terms' => 'contact email phone enquiry help'],
        ];

        $results = collect($pages);
        if ($query !== '') {
            $terms = preg_split('/\s+/', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY);
            $results = $results->map(function ($page) use ($terms) {
                $haystack = mb_strtolower(implode(' ', [$page['title'], $page['category'], $page['excerpt'], $page['terms']]));
                $score = 0;
                foreach ($terms as $term) {
                    $score += str_contains(mb_strtolower($page['title']), $term) ? 5 : (str_contains($haystack, $term) ? 1 : 0);
                }
                $page['score'] = $score;
                return $page;
            })->filter(fn ($page) => $page['score'] > 0)->sortByDesc('score')->values();
        }

        return view('public.search', compact('query', 'results'));
    }

    /**
     * Handle contact form inquiry.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Inquiry::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'type'    => 'contact',
            'message' => $validated['message'],
            'status'  => 'new',
        ]);

        return redirect()->route('public.contact')
            ->with('success', 'Thank you, ' . $validated['name'] . '! Your message has been sent. The Tima-Ade University office will reply to you soon.');
    }

    /**
     * Noticeboard & announcements.
     */
    public function notices(Request $request)
    {
        $query = Notice::where('is_active', true)
            ->where('status', 'published')
            ->whereDate('published_date', '<=', today())
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            });

        $audience = Auth::user()?->isStudent() ? 'students' : (Auth::user()?->isTeacher() ? 'teachers' : (Auth::user()?->isParent() ? 'parents' : (Auth::user()?->isAdmin() ? 'admins' : 'everyone')));
        $query->whereIn('audience', ['everyone', $audience]);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        $notices = $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_date', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = Notice::where('is_active', true)
            ->where('status', 'published')
            ->whereDate('published_date', '<=', today())
            ->where(function ($q) { $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()); })
            ->pluck('category')
            ->unique();

        $featuredNotice = (clone $query)->where('is_pinned', true)->where(function ($q) { $q->whereNotNull('video_path')->orWhereNotNull('external_video_url'); })->first();

        $view = request()->routeIs('public.student-life.news') ? 'public.student-life-news' : 'public.notices';

        return view($view, compact('notices', 'categories', 'featuredNotice'));
    }

    /**
     * View single notice details.
     */
    public function noticeSingle(Notice $notice)
    {
        $audience = Auth::user()?->isStudent() ? 'students' : (Auth::user()?->isTeacher() ? 'teachers' : (Auth::user()?->isParent() ? 'parents' : (Auth::user()?->isAdmin() ? 'admins' : 'everyone')));
        abort_unless($notice->is_active && $notice->status === 'published' && $notice->published_date?->lte(today()) && (!$notice->expires_at || $notice->expires_at->gte(today())) && in_array($notice->audience, ['everyone', $audience]), 404);

        $recentNotices = Notice::where('is_active', true)
            ->where('status', 'published')
            ->whereDate('published_date', '<=', today())
            ->where(function ($q) { $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()); })
            ->where('id', '!=', $notice->id)
            ->orderBy('published_date', 'desc')
            ->take(5)
            ->get();

        return view('public.notice-single', compact('notice', 'recentNotices'));
    }

    public function studentLife()
    {
        return view('public.student-life', ['studentLifeImages' => config('student-life.images')]);
    }

    private function studentLifeInfo(string $page)
    {
        return view('public.student-life-info', [
            'page' => $page,
            'studentLifeImages' => config('student-life.images'),
        ]);
    }

    public function studentLifeAlumni() { return $this->studentLifeInfo('alumni'); }
    public function studentLifeResearch() { return $this->studentLifeInfo('research'); }

    public function studentLifeNews(Request $request)
    {
        return $this->notices($request);
    }

    public function leadership()
    {
        return view('public.leadership');
    }

    public function research()
    {
        return view('public.research');
    }

    public function news(Request $request)
    {
        return $this->notices($request);
    }

    public function events()
    {
        return view('public.events');
    }

    public function scholarships()
    {
        return view('public.scholarships');
    }

    public function careers()
    {
        return view('public.careers');
    }

    public function alumni()
    {
        return view('public.alumni');
    }

    public function library()
    {
        return view('public.library');
    }
}

