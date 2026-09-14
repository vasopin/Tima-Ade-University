<?php

namespace App\Services;

use App\Models\Program;
use App\Models\Student;
use App\Models\StudentHold;
use App\Models\StudentProgramHistory;
use App\Models\StudentStatusHistory;
use App\Models\StudentTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentLifecycleService
{
    private const STATUSES = ['applicant', 'admitted', 'active', 'deferred', 'leave', 'suspended', 'withdrawn', 'graduated', 'dismissed'];

    public function changeStatus(Student $student, string $status, array $data, int $actorId): Student
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages(['status' => 'Invalid lifecycle status.']);
        }
        $current = $student->lifecycle_status ?: match ($student->status) {
            'active' => 'active',
            'graduated' => 'graduated',
            'expelled' => 'dismissed',
            default => 'withdrawn',
        };
        if ($current === $status) {
            throw ValidationException::withMessages(['status' => 'The student already has this status.']);
        }
        $allowed = [
            'applicant' => ['admitted', 'withdrawn'],
            'admitted' => ['active', 'deferred', 'withdrawn'],
            'active' => ['leave', 'suspended', 'withdrawn', 'graduated', 'dismissed'],
            'deferred' => ['active', 'withdrawn'],
            'leave' => ['active', 'withdrawn'],
            'suspended' => ['active', 'withdrawn', 'dismissed'],
            'withdrawn' => ['active'],
            'graduated' => [],
            'dismissed' => [],
        ];
        if (!in_array($status, $allowed[$current] ?? [], true)) {
            throw ValidationException::withMessages(['status' => "Cannot transition from {$current} to {$status}."]);
        }

        return DB::transaction(function () use ($student, $current, $status, $data, $actorId): Student {
            $student->update([
                'lifecycle_status' => $status,
                'status' => match ($status) {
                    'active', 'admitted', 'applicant', 'deferred', 'leave' => 'active',
                    'graduated' => 'graduated',
                    default => 'inactive',
                },
            ]);
            StudentStatusHistory::create([
                'student_id' => $student->id,
                'from_status' => $current,
                'to_status' => $status,
                'effective_date' => $data['effective_date'],
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'actor_id' => $actorId,
            ]);
            return $student->refresh();
        });
    }

    public function changeProgram(Student $student, Program $program, array $data, int $actorId): StudentProgramHistory
    {
        return DB::transaction(function () use ($student, $program, $data, $actorId): StudentProgramHistory {
            $history = StudentProgramHistory::create([
                'student_id' => $student->id,
                'from_program_id' => $student->program_id,
                'to_program_id' => $program->id,
                'effective_date' => $data['effective_date'],
                'reason' => $data['reason'],
                'actor_id' => $actorId,
            ]);
            $student->update(['program_id' => $program->id]);
            return $history;
        });
    }

    public function requestTransfer(Student $student, Program $program, array $data, int $actorId): StudentTransfer
    {
        abort_if($student->program_id === $program->id, 422, 'The student is already assigned to this program.');
        return StudentTransfer::create([
            'student_id' => $student->id,
            'from_program_id' => $student->program_id,
            'to_program_id' => $program->id,
            'type' => $data['type'] ?? 'program',
            'status' => 'requested',
            'effective_date' => $data['effective_date'] ?? null,
            'reason' => $data['reason'],
            'requested_by' => $actorId,
        ]);
    }

    public function reviewTransfer(StudentTransfer $transfer, string $status, int $actorId, ?string $notes = null): StudentTransfer
    {
        abort_unless(in_array($status, ['approved', 'rejected'], true), 422, 'Invalid transfer decision.');
        abort_if($transfer->status !== 'requested', 422, 'This transfer request has already been reviewed.');
        return DB::transaction(function () use ($transfer, $status, $actorId, $notes): StudentTransfer {
            $transfer->update(['status' => $status, 'reviewed_by' => $actorId, 'reviewed_at' => now(), 'notes' => $notes]);
            if ($status === 'approved') {
                $this->changeProgram($transfer->student, $transfer->toProgram, [
                    'effective_date' => $transfer->effective_date ?: now()->toDateString(),
                    'reason' => $transfer->reason,
                ], $actorId);
                $transfer->update(['status' => 'applied']);
            }
            return $transfer->refresh();
        });
    }

    public function placeHold(Student $student, array $data, int $actorId): StudentHold
    {
        return StudentHold::create([
            'student_id' => $student->id,
            'type' => $data['type'],
            'reason' => $data['reason'],
            'status' => 'active',
            'effective_date' => $data['effective_date'] ?? now()->toDateString(),
            'created_by' => $actorId,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function releaseHold(StudentHold $hold, int $actorId): StudentHold
    {
        abort_if($hold->status !== 'active', 422, 'This hold is already released.');
        $hold->update(['status' => 'released', 'release_date' => now()->toDateString(), 'released_by' => $actorId]);
        return $hold->refresh();
    }
}
