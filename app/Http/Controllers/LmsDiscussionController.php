<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionPost;
use App\Services\LmsDiscussionService;
use Illuminate\Http\Request;

class LmsDiscussionController extends Controller
{
    public function __construct(private LmsDiscussionService $discussions) {}

    public function teacherIndex()
    {
        $user = auth()->user();
        abort_unless($user->isTeacher() || $user->isAdmin(), 403);
        $query = LmsDiscussion::with(['subject', 'courseSection.term'])->latest();
        if (!$user->isAdmin()) {
            $query->whereHas('teacher', fn ($teacher) => $teacher->where('user_id', $user->id));
        }
        return view('teacher.discussions', ['discussions' => $query->paginate(20)]);
    }

    public function create()
    {
        abort_unless(auth()->user()->isTeacher(), 403);
        $sections = CourseSection::with(['course', 'term'])->where('teacher_id', auth()->id())->latest()->get();
        return view('teacher.discussion-create', compact('sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'course_section_id' => ['nullable', 'integer', 'exists:course_sections,id'],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'status' => ['required', 'in:draft,published'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
        ]);
        abort_unless($data['course_section_id'] ?? $data['school_class_id'] ?? false, 422, 'A course section or class scope is required.');
        $discussion = $this->discussions->create(auth()->user(), $data);
        return redirect()->route('teacher.discussions.show', $discussion)->with('success', 'Discussion created.');
    }

    public function show(LmsDiscussion $discussion)
    {
        $user = auth()->user();
        abort_unless($this->discussions->canStudentAccess($discussion, $user) || $this->discussions->canTeacherManage($discussion, $user), 403);
        $discussion->load(['teacher.user', 'subject', 'courseSection.course', 'posts' => fn ($query) => $query->where('status', 'visible')->with('author')->oldest()]);
        return view('lms.discussion', compact('discussion'));
    }

    public function post(Request $request, LmsDiscussion $discussion)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000'], 'parent_id' => ['nullable', 'integer']]);
        $this->discussions->post($discussion, auth()->user(), $data['body'], $data['parent_id'] ?? null);
        return back()->with('success', 'Post added.');
    }

    public function studentIndex()
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);
        $discussions = LmsDiscussion::with(['subject', 'courseSection.course'])
            ->where('status', 'published')->latest()->get()
            ->filter(fn (LmsDiscussion $discussion) => $this->discussions->canStudentAccess($discussion, $user));
        return view('student.discussions', compact('discussions'));
    }

    public function close(LmsDiscussion $discussion)
    {
        abort_unless($this->discussions->canTeacherManage($discussion, auth()->user()), 403);
        $discussion->update(['status' => 'closed']);
        return back()->with('success', 'Discussion closed.');
    }

    public function moderate(Request $request, LmsDiscussionPost $post)
    {
        abort_unless($this->discussions->canTeacherManage($post->discussion, auth()->user()), 403);
        $data = $request->validate(['status' => ['required', 'in:visible,hidden']]);
        $post->update(['status' => $data['status']]);
        return back()->with('success', 'Post moderation updated.');
    }

    public function updatePost(Request $request, LmsDiscussionPost $post)
    {
        $user = auth()->user();
        abort_unless((int) $post->author_id === (int) $user->id, 403);
        abort_unless($this->discussions->canStudentAccess($post->discussion, $user) || $this->discussions->canTeacherManage($post->discussion, $user), 403);
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);
        $post->update(['body' => trim($data['body']), 'edited_by' => $user->id, 'edited_at' => now()]);
        return back()->with('success', 'Post updated.');
    }
}
