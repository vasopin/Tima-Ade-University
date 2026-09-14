<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active', 'department_id', 'credits', 'level', 'course_type'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'credits' => 'decimal:2'];
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function lectureVideos(): HasMany
    {
        return $this->hasMany(LectureVideo::class);
    }

    public function courseMaterials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function prerequisites(): HasMany { return $this->hasMany(CoursePrerequisite::class, 'course_id'); }
    public function requiredBy(): HasMany { return $this->hasMany(CoursePrerequisite::class, 'prerequisite_course_id'); }
    public function curricula(): BelongsToMany { return $this->belongsToMany(Curriculum::class, 'curriculum_courses', 'course_id', 'curriculum_id')->withPivot(['is_required', 'recommended_term', 'credits'])->withTimestamps(); }
    public function courseSections(): HasMany { return $this->hasMany(CourseSection::class, 'course_id'); }
}
