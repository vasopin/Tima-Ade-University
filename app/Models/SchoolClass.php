<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClass extends Model
{
    protected $table    = 'school_classes';
    protected $fillable = ['name', 'grade_level', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function classTeacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'class_teacher_of');
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
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
}
