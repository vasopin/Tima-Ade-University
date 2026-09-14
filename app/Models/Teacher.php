<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'qualification', 'specialization',
        'joining_date', 'address', 'gender', 'date_of_birth',
        'emergency_contact', 'is_class_teacher', 'class_teacher_of',
    ];

    protected function casts(): array
    {
        return [
            'joining_date'     => 'date',
            'date_of_birth'    => 'date',
            'is_class_teacher' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classTeacherOf(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_teacher_of');
    }

    public function attendancesMarked(): HasMany
    {
        return $this->hasMany(Attendance::class, 'marked_by', 'user_id');
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

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'teacher_id', 'user_id');
    }
}
