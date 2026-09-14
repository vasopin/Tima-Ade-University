<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\LectureVideo;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LearningMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeVideo(Request $request)
    {
        $this->authorizeTeacher();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'video' => [$request->filled('video_id') ? 'nullable' : 'required', 'file', 'mimes:mp4,mov,avi,mkv,webm', 'max:204800'],
            'video_id' => ['nullable', 'exists:lecture_videos,id'],
        ]);

        $user = auth()->user();
        $teacher = $user && $user->isAdmin() ? null : $this->teacherProfile();
        $this->authorizeTeacherCourseAccess($teacher, $validated['school_class_id'], $validated['subject_id']);

        $class = SchoolClass::findOrFail($validated['school_class_id']);
        $subject = Subject::findOrFail($validated['subject_id']);

        $videoModel = $request->filled('video_id')
            ? ($teacher
                ? LectureVideo::where('teacher_id', $teacher->id)->findOrFail($validated['video_id'])
                : LectureVideo::findOrFail($validated['video_id']))
            : new LectureVideo();

        $path = $videoModel->file_path ?? null;
        if ($request->hasFile('video')) {
            $path = $request->file('video')->storeAs('uploads/videos', Str::slug($validated['title']) . '-' . time() . '.' . $request->file('video')->getClientOriginalExtension(), 'private');
        }

        $videoModel->fill([
            'teacher_id' => $teacher?->id,
            'school_class_id' => $validated['school_class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_name' => $request->hasFile('video') ? $request->file('video')->getClientOriginalName() : ($videoModel->original_name ?? 'Uploaded video'),
            'mime_type' => $request->hasFile('video') ? $request->file('video')->getMimeType() : ($videoModel->mime_type ?? 'video/mp4'),
            'file_size' => $request->hasFile('video') ? $request->file('video')->getSize() : ($videoModel->file_size ?? 0),
            'status' => 'published',
        ]);
        $videoModel->save();

        if (! $request->filled('video_id')) {
            $this->notifyStudentsForClass($class, $subject, 'New lecture video uploaded', 'A new lecture video titled "' . $validated['title'] . '" is now available in ' . $class->name . '.', 'video');
        }

        return back()->with('success', $request->filled('video_id') ? 'Lecture video updated successfully.' : 'Lecture video uploaded successfully.');
    }

    public function storeMaterial(Request $request)
    {
        $this->authorizeTeacher();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'material' => [$request->filled('material_id') ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:20000'],
            'material_id' => ['nullable', 'exists:course_materials,id'],
        ]);

        $user = auth()->user();
        $teacher = $user && $user->isAdmin() ? null : $this->teacherProfile();
        $this->authorizeTeacherCourseAccess($teacher, $validated['school_class_id'], $validated['subject_id']);

        $class = SchoolClass::findOrFail($validated['school_class_id']);
        $subject = Subject::findOrFail($validated['subject_id']);

        $materialModel = $request->filled('material_id')
            ? ($teacher
                ? CourseMaterial::where('teacher_id', $teacher->id)->findOrFail($validated['material_id'])
                : CourseMaterial::findOrFail($validated['material_id']))
            : new CourseMaterial();

        $path = $materialModel->file_path ?? null;
        if ($request->hasFile('material')) {
            $path = $request->file('material')->storeAs('uploads/materials', Str::slug($validated['title']) . '-' . time() . '.pdf', 'private');
        }

        $materialModel->fill([
            'teacher_id' => $teacher?->id,
            'school_class_id' => $validated['school_class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_name' => $request->hasFile('material') ? $request->file('material')->getClientOriginalName() : ($materialModel->original_name ?? 'Uploaded material'),
            'mime_type' => $request->hasFile('material') ? $request->file('material')->getMimeType() : ($materialModel->mime_type ?? 'application/pdf'),
            'file_size' => $request->hasFile('material') ? $request->file('material')->getSize() : ($materialModel->file_size ?? 0),
            'status' => 'published',
        ]);
        $materialModel->save();

        if (! $request->filled('material_id')) {
            $this->notifyStudentsForClass($class, $subject, 'New course material uploaded', 'A new PDF learning material titled "' . $validated['title'] . '" is now available in ' . $class->name . '.', 'material');
        }

        return back()->with('success', $request->filled('material_id') ? 'Course material updated successfully.' : 'Course material uploaded successfully.');
    }

    public function storeAssignment(Request $request)
    {
        $this->authorizeTeacher();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'due_date' => ['nullable', 'date'],
            'available_from' => ['nullable', 'date'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:20'],
            'max_points' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'status' => ['nullable', 'in:draft,published'],
            'assignment_file' => [$request->filled('assignment_id') ? 'nullable' : 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:20000'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
        ]);

        $user = auth()->user();
        $teacher = $user && $user->isAdmin() ? null : $this->teacherProfile();
        $this->authorizeTeacherCourseAccess($teacher, $validated['school_class_id'], $validated['subject_id']);

        $class = SchoolClass::findOrFail($validated['school_class_id']);
        $subject = Subject::findOrFail($validated['subject_id']);

        $assignmentModel = $request->filled('assignment_id')
            ? ($teacher
                ? Assignment::where('teacher_id', $teacher->id)->findOrFail($validated['assignment_id'])
                : Assignment::findOrFail($validated['assignment_id']))
            : new Assignment();

        $path = $assignmentModel->file_path ?? null;
        if ($request->hasFile('assignment_file')) {
            $path = $request->file('assignment_file')->storeAs('uploads/assignments', Str::slug($validated['title']) . '-' . time() . '.' . $request->file('assignment_file')->getClientOriginalExtension(), 'private');
        }

        $assignmentModel->fill([
            'teacher_id' => $teacher?->id,
            'school_class_id' => $validated['school_class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? now()->addWeek(),
            'available_from' => $validated['available_from'] ?? null,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'max_points' => $validated['max_points'] ?? 100,
            'published_at' => ($validated['status'] ?? 'published') === 'published' ? ($assignmentModel->published_at ?? now()) : null,
            'file_path' => $path,
            'original_name' => $request->hasFile('assignment_file') ? $request->file('assignment_file')->getClientOriginalName() : ($assignmentModel->original_name ?? 'Assignment file'),
            'mime_type' => $request->hasFile('assignment_file') ? $request->file('assignment_file')->getMimeType() : ($assignmentModel->mime_type ?? 'application/pdf'),
            'file_size' => $request->hasFile('assignment_file') ? $request->file('assignment_file')->getSize() : ($assignmentModel->file_size ?? 0),
            'status' => $validated['status'] ?? 'published',
        ]);
        $assignmentModel->save();

        if (! $request->filled('assignment_id') && ($validated['status'] ?? 'published') === 'published') {
            $this->notifyStudentsForClass($class, $subject, 'New assignment posted', 'A new assignment titled "' . $validated['title'] . '" has been posted for ' . $class->name . '.', 'assignment');
        }

        return back()->with('success', $request->filled('assignment_id') ? 'Assignment updated successfully.' : 'Assignment uploaded successfully.');
    }

    public function destroyVideo(LectureVideo $video)
    {
        $this->authorizeTeacher();

        if (! auth()->user()->isAdmin() && $video->teacher_id !== $this->teacherProfile()->id) {
            abort(403, 'You can only delete your own lecture videos.');
        }

        foreach (['private', 'public'] as $disk) {
            if ($video->file_path && Storage::disk($disk)->exists($video->file_path)) {
                Storage::disk($disk)->delete($video->file_path);
            }
        }

        $video->delete();

        return back()->with('success', 'Lecture video deleted successfully.');
    }

    public function destroyMaterial(CourseMaterial $material)
    {
        $this->authorizeTeacher();

        if (! auth()->user()->isAdmin() && $material->teacher_id !== $this->teacherProfile()->id) {
            abort(403, 'You can only delete your own course materials.');
        }

        foreach (['private', 'public'] as $disk) {
            if ($material->file_path && Storage::disk($disk)->exists($material->file_path)) {
                Storage::disk($disk)->delete($material->file_path);
            }
        }

        $material->delete();

        return back()->with('success', 'Course material deleted successfully.');
    }

    public function destroyAssignment(Assignment $assignment)
    {
        $this->authorizeTeacher();

        if (! auth()->user()->isAdmin() && $assignment->teacher_id !== $this->teacherProfile()->id) {
            abort(403, 'You can only delete your own assignments.');
        }

        foreach (['private', 'public'] as $disk) {
            if ($assignment->file_path && Storage::disk($disk)->exists($assignment->file_path)) {
                Storage::disk($disk)->delete($assignment->file_path);
            }
        }

        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully.');
    }

    public function viewVideo(LectureVideo $video)
    {
        $this->authorizeLmsAccess($video, 'video');

        return $this->serveFile($video->file_path, $video->mime_type ?? 'video/mp4', $video->title ?? 'Lecture video');
    }

    public function viewMaterial(CourseMaterial $material)
    {
        $this->authorizeLmsAccess($material, 'material');

        return $this->serveFile($material->file_path, $material->mime_type ?? 'application/pdf', $material->title ?? 'Course material');
    }

    public function viewAssignment(Assignment $assignment)
    {
        $this->authorizeLmsAccess($assignment, 'assignment');

        return $this->serveFile($assignment->file_path, $assignment->mime_type ?? 'application/pdf', $assignment->title ?? 'Assignment');
    }

    protected function authorizeTeacher(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isTeacher() && ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Teacher access required.');
        }
    }

    protected function authorizeTeacherCourseAccess(?Teacher $teacher, int $classId, int $subjectId): void
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            return;
        }

        if (! $teacher) {
            abort(403, 'Teacher profile not found.');
        }

        $allowedClassIds = $this->authorizedTeacherClassIds($teacher);
        if (! in_array($classId, $allowedClassIds, true)) {
            abort(403, 'You are not authorized to manage this class.');
        }

        $pivotMatch = DB::table('class_subject')
            ->where('school_class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where(function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->user_id)
                    ->orWhere('school_class_id', $teacher->class_teacher_of);
            })
            ->exists();

        if (! $pivotMatch) {
            abort(403, 'You are not authorized for this course.');
        }
    }

    protected function authorizedTeacherClassIds(Teacher $teacher): array
    {
        $classIds = [$teacher->class_teacher_of];

        $assignedIds = DB::table('class_subject')
            ->where('teacher_id', $teacher->user_id)
            ->pluck('school_class_id')
            ->all();

        foreach ($assignedIds as $id) {
            $classIds[] = $id;
        }

        return array_values(array_unique(array_filter(array_map('intval', $classIds))));
    }

    protected function teacherProfile(): Teacher
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (! $teacher) {
            abort(403, 'Teacher profile not found.');
        }

        return $teacher;
    }

    protected function authorizeLmsAccess($model, string $type): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(403, 'Authentication required.');
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTeacher()) {
            $teacher = $this->teacherProfile();
            $isOwner = $model->teacher_id === $teacher->id;
            $allowedClassIds = $this->authorizedTeacherClassIds($teacher);
            $inAuthorizedClass = in_array((int) $model->school_class_id, $allowedClassIds, true);

            if (! $isOwner && ! $inAuthorizedClass) {
                abort(403, 'You do not have access to this learning material.');
            }

            return;
        }

        if ($user->isStudent()) {
            $student = $user->student;
            if (! $student || (int) $student->school_class_id !== (int) $model->school_class_id) {
                abort(403, 'This learning material is not available for your enrolled class.');
            }

            return;
        }

        abort(403, 'Unauthorized access.');
    }

    protected function serveFile(?string $path, string $mimeType, string $label): mixed
    {
        if (! $path) {
            abort(404, 'The requested ' . strtolower($label) . ' is unavailable or has been deleted.');
        }

        $disk = Storage::disk('private')->exists($path) ? 'private' : 'public';
        if (! Storage::disk($disk)->exists($path)) {
            abort(404, 'The requested ' . strtolower($label) . ' is unavailable or has been deleted.');
        }

        return response()->file(Storage::disk($disk)->path($path), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    protected function notifyStudentsForClass(SchoolClass $class, Subject $subject, string $title, string $body, string $type): void
    {
        $students = Student::with('user')->where('school_class_id', $class->id)->get();

        foreach ($students as $student) {
            if (! $student->user) {
                continue;
            }

            Notification::create([
                'user_id' => $student->user_id,
                'title' => $title,
                'body' => $body,
                'role' => 'student',
                'meta' => [
                    'type' => $type,
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                ],
            ]);
        }
    }
}
