<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsDiscussion extends Model
{
    protected $table = 'lms_discussions';
    protected $fillable = ['teacher_id', 'course_section_id', 'school_class_id', 'subject_id', 'title', 'description', 'status', 'available_from', 'available_until'];

    protected function casts(): array
    {
        return ['available_from' => 'datetime', 'available_until' => 'datetime'];
    }

    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function courseSection(): BelongsTo { return $this->belongsTo(CourseSection::class); }
    public function schoolClass(): BelongsTo { return $this->belongsTo(SchoolClass::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function posts(): HasMany { return $this->hasMany(LmsDiscussionPost::class, 'discussion_id'); }

    public function isAvailable(): bool
    {
        return $this->status === 'published'
            && (!$this->available_from || now()->greaterThanOrEqualTo($this->available_from))
            && (!$this->available_until || now()->lessThanOrEqualTo($this->available_until));
    }

    public function isOpen(): bool { return $this->status === 'published'; }
}
