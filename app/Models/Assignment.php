<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'school_class_id',
        'subject_id',
        'title',
        'description',
        'due_date',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status', 'available_from', 'max_attempts', 'max_points', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'available_from' => 'datetime',
            'published_at' => 'datetime',
            'file_size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
    public function rubric(): HasOne { return $this->hasOne(Rubric::class); }

    public function getFileUrlAttribute(): string
    {
        return $this->file_path ? route('learning.assignment.file', ['assignment' => $this->id]) : '';
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
