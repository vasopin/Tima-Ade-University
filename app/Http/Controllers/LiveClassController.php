<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\LiveClassActivity;
use App\Models\LiveClassMessage;
use App\Models\LiveClassParticipant;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\LiveClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiveClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $teacherClasses = $this->teacherClasses($user);
        $classes = LiveClass::with(['subject', 'schoolClass', 'participants.user'])
            ->where('teacher_id', $user->id)
            ->orderBy('scheduled_at')
            ->get();

        return view('teacher.live-classes', compact('classes', 'teacherClasses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $subject = Subject::findOrFail($data['subject_id']);

        $isTeacherForClass = DB::table('class_subject')
            ->where('school_class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->where('teacher_id', $user->id)
            ->exists();

        abort_unless($isTeacherForClass, 403, 'You are not authorized to create a live class for this course.');

        $liveClass = LiveClass::create([
            'teacher_id' => $user->id,
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'title' => $data['title'],
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $data['duration_minutes'],
            'status' => 'scheduled',
        ]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'class_created',
            'details' => 'Live class scheduled',
        ]);

        return redirect()->route('teacher.live-classes.index')->with('success', 'Live class scheduled successfully.');
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher(), 403, 'Teacher access required.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ]);

        $this->abortUnlessTeacherCourse($user, $data['school_class_id'], $data['subject_id']);

        $liveClass = LiveClass::create([
            ...$data,
            'teacher_id' => $user->id,
            'scheduled_at' => now(),
            'duration_minutes' => 45,
            'status' => 'created',
        ]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'class_created',
            'details' => 'Live classroom created and ready to schedule',
        ]);

        return redirect()->route('teacher.live-classes.index')->with('success', 'Live classroom created. Add a date and time to schedule it.');
    }

    public function schedule(Request $request, LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher access required.');
        abort_unless($liveClass->status === 'created', 422, 'Only created classrooms can be scheduled.');

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
        ]);

        $liveClass->update([...$data, 'status' => 'scheduled']);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'class_scheduled',
            'details' => 'Live classroom scheduled',
        ]);

        return redirect()->route('teacher.live-classes.index')->with('success', 'Live class scheduled successfully.');
    }

    public function show(LiveClass $liveClass, LiveClassroomService $service)
    {
        $user = Auth::user();
        abort_unless($user, 403, 'Authentication required.');

        $canAccess = $user->isTeacher() && $liveClass->teacher_id === $user->id
            || $user->isStudent() && $this->studentCanAccessClass($user, $liveClass)
            || $user->isAdmin();

        abort_unless($canAccess, 403, 'You do not have access to this classroom.');
        abort_unless($liveClass->status === 'live' || ! $user->isStudent(), 403, 'This classroom is not active.');

        $liveClass->load(['teacher', 'subject', 'schoolClass', 'participants.user', 'messages.user']);
        $classroomMeta = $service->classroomMetadata($liveClass, $user);

        return view('live-class.show', compact('liveClass', 'classroomMeta'));
    }

    public function start(LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher access required.');
        abort_unless($liveClass->status === 'scheduled', 422, 'Only scheduled classes can be started.');

        $liveClass->update([
            'status' => 'live',
            'started_at' => now(),
        ]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'class_started',
            'details' => 'Teacher started the live class',
        ]);

        return redirect()->route('live-classes.show', $liveClass)
            ->with('success', 'Live class started.');
    }

    public function end(LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher access required.');
        abort_unless($liveClass->status === 'live', 422, 'Only live classes can be ended.');

        $liveClass->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);
        $liveClass->participants()->where('is_active', true)->update([
            'is_active' => false,
            'left_at' => now(),
        ]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'class_ended',
            'details' => 'Teacher ended the live class',
        ]);

        return back()->with('success', 'Live class ended.');
    }

    public function join(LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403, 'Student access required.');
        abort_unless($this->studentCanAccessClass($user, $liveClass), 403, 'You are not enrolled in this course.');
        abort_unless($liveClass->status === 'live', 403, 'This classroom is not active.');

        $existing = LiveClassParticipant::where('live_class_id', $liveClass->id)
            ->where('user_id', $user->id)
            ->first();

        abort_unless(! $existing || ! $existing->removed_at, 403, 'You have been removed from this classroom.');

        if ($existing) {
            $existing->update(['is_active' => true, 'left_at' => null, 'removed_at' => null, 'joined_at' => now()]);
        } else {
            LiveClassParticipant::create([
                'live_class_id' => $liveClass->id,
                'user_id' => $user->id,
                'role' => 'student',
                'is_active' => true,
                'is_muted' => false,
                'camera_on' => true,
                'screen_sharing' => false,
                'joined_at' => now(),
            ]);
        }

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'participant_joined',
            'details' => 'Student joined the classroom',
        ]);

        return redirect()->route('live-classes.show', $liveClass)->with('success', 'You joined the live classroom.');
    }

    public function storeMessage(Request $request, LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && ($user->isTeacher() || $user->isStudent()), 403, 'Classroom access required.');
        abort_unless($liveClass->status === 'live', 422, 'Chat is unavailable outside a live class.');
        abort_unless($this->userCanJoinClassroom($user, $liveClass), 403, 'You are not a member of this classroom.');

        $participant = $liveClass->participants()->where('user_id', $user->id)->first();
        abort_unless($user->isTeacher() || ($participant && $participant->is_active), 403, 'Join the classroom before sending messages.');
        abort_unless($user->isTeacher() || data_get($participant->permissions, 'can_send_messages', true), 403, 'Messaging is disabled for you.');

        $message = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        LiveClassMessage::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => trim($message['message']),
        ]);

        return back()->with('success', 'Message sent.');
    }

    public function leave(LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($user && $this->userCanJoinClassroom($user, $liveClass), 403, 'Classroom access required.');

        $participant = $liveClass->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['is_active' => false, 'left_at' => now()]);
        }

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'participant_left',
            'details' => 'Participant left the classroom',
        ]);

        return redirect()->route($user->isTeacher() ? 'teacher.live-classes.index' : 'student.dashboard')
            ->with('success', 'You left the classroom.');
    }

    public function updateMedia(Request $request, LiveClass $liveClass)
    {
        $user = Auth::user();
        abort_unless($liveClass->status === 'live', 422, 'Media controls are unavailable outside a live class.');
        abort_unless($user && $this->userCanJoinClassroom($user, $liveClass), 403, 'Classroom access required.');

        $participant = $liveClass->participants()->where('user_id', $user->id)->first();
        abort_unless($user->isTeacher() || ($participant && $participant->is_active && ! $participant->removed_at), 403, 'Join the classroom before changing media.');

        $data = $request->validate([
            'is_muted' => ['sometimes', 'boolean'],
            'camera_on' => ['sometimes', 'boolean'],
            'screen_sharing' => ['sometimes', 'boolean'],
        ]);

        abort_unless(! array_key_exists('screen_sharing', $data) || $user->isTeacher() || data_get($participant->permissions, 'can_share_screen', true), 403, 'Screen sharing is disabled for you.');

        $liveClass->participants()->updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['role' => $user->isTeacher() ? 'teacher' : 'student', 'is_active' => true, 'joined_at' => now()])
        );

        return response()->json(['ok' => true, 'media' => $data]);
    }

    public function muteParticipant(LiveClass $liveClass, LiveClassParticipant $participant)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher moderation required.');
        abort_unless($participant->live_class_id === $liveClass->id, 403, 'Participant does not belong to this classroom.');

        $participant->update(['is_muted' => ! $participant->is_muted]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'participant_muted',
            'details' => 'Participant mute toggled',
        ]);

        return back()->with('success', 'Participant mute state updated.');
    }

    public function removeParticipant(LiveClass $liveClass, LiveClassParticipant $participant)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher moderation required.');
        abort_unless($participant->live_class_id === $liveClass->id, 403, 'Participant does not belong to this classroom.');

        $participant->update([
            'is_active' => false,
            'removed_at' => now(),
            'removed_by' => $user->id,
            'left_at' => now(),
        ]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'participant_removed',
            'details' => 'Participant removed from classroom',
        ]);

        return back()->with('success', 'Participant removed.');
    }

    public function updateParticipantPermissions(Request $request, LiveClass $liveClass, LiveClassParticipant $participant)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTeacher() && $liveClass->teacher_id === $user->id, 403, 'Teacher moderation required.');
        abort_unless($participant->live_class_id === $liveClass->id, 403, 'Participant does not belong to this classroom.');

        $data = $request->validate([
            'can_unmute' => ['sometimes', 'boolean'],
            'can_share_screen' => ['sometimes', 'boolean'],
            'can_send_messages' => ['sometimes', 'boolean'],
        ]);

        $participant->update(['permissions' => $data]);

        LiveClassActivity::create([
            'live_class_id' => $liveClass->id,
            'actor_id' => $user->id,
            'action' => 'participant_permissions_updated',
            'details' => 'Participant classroom permissions updated',
        ]);

        return back()->with('success', 'Participant permissions updated.');
    }

    protected function teacherClasses(User $user)
    {
        return SchoolClass::with(['subjects' => function ($query) use ($user) {
            $query->wherePivot('teacher_id', $user->id);
        }])
            ->whereHas('subjects', function ($query) use ($user) {
                $query->where('class_subject.teacher_id', $user->id);
            })
            ->orderBy('name')
            ->get();
    }

    protected function abortUnlessTeacherCourse(User $user, int $schoolClassId, int $subjectId): void
    {
        abort_unless(DB::table('class_subject')
            ->where('school_class_id', $schoolClassId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', $user->id)
            ->exists(), 403, 'You are not authorized to create a live class for this course.');
    }

    protected function studentCanAccessClass(User $user, LiveClass $liveClass): bool
    {
        $student = Student::where('user_id', $user->id)->first();
        if (! $student) {
            return false;
        }

        return (int) $student->school_class_id === (int) $liveClass->school_class_id;
    }

    protected function userCanJoinClassroom(User $user, LiveClass $liveClass): bool
    {
        if ($user->isTeacher() && $liveClass->teacher_id === $user->id) {
            return true;
        }

        if ($user->isStudent()) {
            return $this->studentCanAccessClass($user, $liveClass);
        }

        return false;
    }
}
