<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $fillable = ['course_id', 'term_id', 'teacher_id', 'code', 'room', 'capacity', 'status'];
    public function course(): BelongsTo { return $this->belongsTo(Subject::class, 'course_id'); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }
}
