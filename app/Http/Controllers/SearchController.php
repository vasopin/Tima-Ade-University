<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Guardian;
use App\Models\LectureVideo;
use App\Models\LiveClass;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Search dashboard data based on user role
     * Results are filtered by user's existing permissions
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403, 'Unauthorized');

        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'message' => 'Query must be at least 2 characters']);
        }

        $results = [];

        // Super Admin: Search all institutional data
        if ($user->isSuperAdmin()) {
            $results = $this->searchSuperAdmin($query);
        }
        // Admin: Search admin-authorized data
        elseif ($user->isAdmin()) {
            $results = $this->searchAdmin($query);
        }
        // Staff: Search staff-authorized data
        elseif ($user->isStaff()) {
            $results = $this->searchStaff($query);
        }
        // Teacher: Search own courses, classes, students
        elseif ($user->isTeacher()) {
            $results = $this->searchTeacher($query, $user);
        }
        // Student: Search own courses, assignments, schedule
        elseif ($user->isStudent()) {
            $results = $this->searchStudent($query, $user);
        }
        // Parent: Search children's data
        elseif ($user->isParent()) {
            $results = $this->searchParent($query, $user);
        }

        return response()->json(['results' => $results], 200);
    }

    /**
     * Super Admin search: All institutional data
     */
    protected function searchSuperAdmin(string $query): array
    {
        $results = [];

        // Search users
        $users = User::where('name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'type' => 'user',
                'id' => $user->id,
                'title' => $user->name,
                'subtitle' => $user->email . ' (' . ($user->role?->name ?? 'No Role') . ')',
                'icon' => 'user'
            ];
        }

        // Search classes
        $classes = SchoolClass::where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($classes as $class) {
            $results[] = [
                'type' => 'class',
                'id' => $class->id,
                'title' => $class->name,
                'subtitle' => 'Grade ' . ($class->grade_level ?? 'N/A'),
                'icon' => 'building'
            ];
        }

        // Search subjects
        $subjects = Subject::where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($subjects as $subject) {
            $results[] = [
                'type' => 'subject',
                'id' => $subject->id,
                'title' => $subject->name,
                'subtitle' => $subject->code ?? 'N/A',
                'icon' => 'book'
            ];
        }

        // Search events
        $events = Event::where('title', 'like', "%$query%")
            ->where('is_active', true)
            ->limit(10)
            ->get();

        foreach ($events as $event) {
            $results[] = [
                'type' => 'event',
                'id' => $event->id,
                'title' => $event->title,
                'subtitle' => 'Event · ' . ($event->starts_at?->format('M d, Y') ?? 'N/A'),
                'icon' => 'calendar'
            ];
        }

        return array_slice($results, 0, 20);
    }

    /**
     * Admin search: Admin-authorized data
     */
    protected function searchAdmin(string $query): array
    {
        $results = [];

        // Search students
        $students = Student::whereHas('user', function ($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })->with(['user', 'schoolClass'])
            ->limit(10)
            ->get();

        foreach ($students as $student) {
            $results[] = [
                'type' => 'student',
                'id' => $student->id,
                'title' => $student->user?->name ?? 'Unknown',
                'subtitle' => 'Student · ' . ($student->schoolClass?->name ?? 'N/A'),
                'icon' => 'user-graduate'
            ];
        }

        // Search teachers
        $teachers = Teacher::whereHas('user', function ($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })->with(['user', 'schoolClass'])
            ->limit(10)
            ->get();

        foreach ($teachers as $teacher) {
            $results[] = [
                'type' => 'teacher',
                'id' => $teacher->id,
                'title' => $teacher->user?->name ?? 'Unknown',
                'subtitle' => 'Teacher · ' . ($teacher->schoolClass?->name ?? 'Class Teacher'),
                'icon' => 'chalkboard-user'
            ];
        }

        // Search classes
        $classes = SchoolClass::where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($classes as $class) {
            $results[] = [
                'type' => 'class',
                'id' => $class->id,
                'title' => $class->name,
                'subtitle' => 'Grade ' . ($class->grade_level ?? 'N/A'),
                'icon' => 'building'
            ];
        }

        // Search exams
        $exams = Exam::where('status', '!=', 'cancelled')
            ->whereHas('subject', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

        foreach ($exams as $exam) {
            $results[] = [
                'type' => 'exam',
                'id' => $exam->id,
                'title' => ($exam->subject?->name ?? 'Exam'),
                'subtitle' => 'Exam · ' . ($exam->exam_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'pencil'
            ];
        }

        // Search notices
        $notices = Notice::where('title', 'like', "%$query%")
            ->where('is_active', true)
            ->where('status', 'published')
            ->limit(10)
            ->get();

        foreach ($notices as $notice) {
            $results[] = [
                'type' => 'notice',
                'id' => $notice->id,
                'title' => $notice->title,
                'subtitle' => 'Notice · ' . ($notice->published_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'bell'
            ];
        }

        return array_slice($results, 0, 20);
    }

    /**
     * Staff search: Staff-authorized data
     */
    protected function searchStaff(string $query): array
    {
        // Similar to admin search for now
        return $this->searchAdmin($query);
    }

    /**
     * Teacher search: Own courses, classes, students
     */
    protected function searchTeacher(string $query, User $user): array
    {
        $results = [];
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return [];
        }

        // Search own subjects/courses
        $subjects = Subject::whereHas('classes', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($subjects as $subject) {
            $results[] = [
                'type' => 'subject',
                'id' => $subject->id,
                'title' => $subject->name,
                'subtitle' => $subject->code ?? 'Subject',
                'icon' => 'book'
            ];
        }

        // Search students in own classes
        $students = Student::whereHas('schoolClass', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->whereHas('user', function ($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })->with(['user', 'schoolClass'])
            ->limit(10)
            ->get();

        foreach ($students as $student) {
            $results[] = [
                'type' => 'student',
                'id' => $student->id,
                'title' => $student->user?->name ?? 'Unknown',
                'subtitle' => 'Student · ' . ($student->schoolClass?->name ?? 'N/A'),
                'icon' => 'user-graduate'
            ];
        }

        // Search own live classes
        $liveClasses = LiveClass::where('teacher_id', $teacher->id)
            ->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($liveClasses as $lc) {
            $results[] = [
                'type' => 'live-class',
                'id' => $lc->id,
                'title' => $lc->title ?? 'Live Class',
                'subtitle' => 'Live Class · ' . ($lc->scheduled_at?->format('M d, Y') ?? 'N/A'),
                'icon' => 'video'
            ];
        }

        // Search lecture videos
        $videos = LectureVideo::where('teacher_id', $teacher->id)
            ->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($videos as $video) {
            $results[] = [
                'type' => 'video',
                'id' => $video->id,
                'title' => $video->title ?? 'Lecture Video',
                'subtitle' => 'Video · ' . ($video->uploaded_at?->format('M d, Y') ?? 'N/A'),
                'icon' => 'film'
            ];
        }

        return array_slice($results, 0, 20);
    }

    /**
     * Student search: Own courses, assignments, schedule
     */
    protected function searchStudent(string $query, User $user): array
    {
        $results = [];
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return [];
        }

        // Search subjects in own class
        $subjects = Subject::whereHas('classes', function ($q) use ($student) {
            $q->where('school_class_id', $student->school_class_id);
        })->where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($subjects as $subject) {
            $results[] = [
                'type' => 'subject',
                'id' => $subject->id,
                'title' => $subject->name,
                'subtitle' => $subject->code ?? 'Subject',
                'icon' => 'book'
            ];
        }

        // Search assignments
        $assignments = Assignment::whereHas('subject', function ($q) use ($student) {
            $q->whereHas('classes', function ($q2) use ($student) {
                $q2->where('school_class_id', $student->school_class_id);
            });
        })->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($assignments as $assignment) {
            $results[] = [
                'type' => 'assignment',
                'id' => $assignment->id,
                'title' => $assignment->title ?? 'Assignment',
                'subtitle' => 'Due: ' . ($assignment->due_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'list-check'
            ];
        }

        // Search course materials
        $materials = CourseMaterial::whereHas('subject', function ($q) use ($student) {
            $q->whereHas('classes', function ($q2) use ($student) {
                $q2->where('school_class_id', $student->school_class_id);
            });
        })->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($materials as $material) {
            $results[] = [
                'type' => 'material',
                'id' => $material->id,
                'title' => $material->title ?? 'Course Material',
                'subtitle' => 'Material · ' . ($material->uploaded_at?->format('M d, Y') ?? 'N/A'),
                'icon' => 'file-text'
            ];
        }

        // Search exams
        $exams = Exam::whereHas('subject', function ($q) use ($student) {
            $q->whereHas('classes', function ($q2) use ($student) {
                $q2->where('school_class_id', $student->school_class_id);
            });
        })->where('status', '!=', 'cancelled')
            ->limit(10)
            ->get();

        foreach ($exams as $exam) {
            $results[] = [
                'type' => 'exam',
                'id' => $exam->id,
                'title' => ($exam->subject?->name ?? 'Exam'),
                'subtitle' => 'Exam · ' . ($exam->exam_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'pencil'
            ];
        }

        // Search live classes
        $liveClasses = LiveClass::whereHas('subject', function ($q) use ($student) {
            $q->whereHas('classes', function ($q2) use ($student) {
                $q2->where('school_class_id', $student->school_class_id);
            });
        })->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($liveClasses as $lc) {
            $results[] = [
                'type' => 'live-class',
                'id' => $lc->id,
                'title' => $lc->title ?? 'Live Class',
                'subtitle' => 'Live Class · ' . ($lc->scheduled_at?->format('M d, Y') ?? 'N/A'),
                'icon' => 'video'
            ];
        }

        return array_slice($results, 0, 20);
    }

    /**
     * Parent search: Children's data
     */
    protected function searchParent(string $query, User $user): array
    {
        $results = [];
        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            return [];
        }

        $studentIds = $guardian->students()->pluck('id');

        // Search own children
        $children = Student::whereIn('id', $studentIds)
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })->with(['user', 'schoolClass'])
            ->limit(10)
            ->get();

        foreach ($children as $child) {
            $results[] = [
                'type' => 'student',
                'id' => $child->id,
                'title' => $child->user?->name ?? 'Unknown',
                'subtitle' => 'Child · ' . ($child->schoolClass?->name ?? 'N/A'),
                'icon' => 'user-graduate'
            ];
        }

        // Search assignments for children
        $assignments = Assignment::whereHas('subject', function ($q) use ($studentIds) {
            $q->whereHas('classes', function ($q2) use ($studentIds) {
                $q2->whereHas('students', function ($q3) use ($studentIds) {
                    $q3->whereIn('student_id', $studentIds);
                });
            });
        })->where('title', 'like', "%$query%")
            ->limit(10)
            ->get();

        foreach ($assignments as $assignment) {
            $results[] = [
                'type' => 'assignment',
                'id' => $assignment->id,
                'title' => $assignment->title ?? 'Assignment',
                'subtitle' => 'Due: ' . ($assignment->due_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'list-check'
            ];
        }

        // Search exams for children
        $exams = Exam::whereHas('subject', function ($q) use ($studentIds) {
            $q->whereHas('classes', function ($q2) use ($studentIds) {
                $q2->whereHas('students', function ($q3) use ($studentIds) {
                    $q3->whereIn('student_id', $studentIds);
                });
            });
        })->where('status', '!=', 'cancelled')
            ->limit(10)
            ->get();

        foreach ($exams as $exam) {
            $results[] = [
                'type' => 'exam',
                'id' => $exam->id,
                'title' => ($exam->subject?->name ?? 'Exam'),
                'subtitle' => 'Exam · ' . ($exam->exam_date?->format('M d, Y') ?? 'N/A'),
                'icon' => 'pencil'
            ];
        }

        return array_slice($results, 0, 20);
    }
}
