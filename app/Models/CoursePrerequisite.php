<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class CoursePrerequisite extends Model
{
    protected $fillable = ['course_id', 'prerequisite_course_id', 'minimum_grade'];
    protected static function booted(): void
    {
        static::saving(function (CoursePrerequisite $prerequisite): void {
            if ($prerequisite->course_id === $prerequisite->prerequisite_course_id) {
                throw ValidationException::withMessages(['prerequisite_course_id' => 'A course cannot be its own prerequisite.']);
            }
        });
    }
    public function course(): BelongsTo { return $this->belongsTo(Subject::class, 'course_id'); }
    public function prerequisiteCourse(): BelongsTo { return $this->belongsTo(Subject::class, 'prerequisite_course_id'); }
}
