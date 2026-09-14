<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Term extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'code', 'starts_on', 'ends_on', 'registration_starts_on', 'registration_ends_on', 'status', 'is_current'];

    protected function casts(): array { return ['starts_on' => 'date', 'ends_on' => 'date', 'registration_starts_on' => 'date', 'registration_ends_on' => 'date', 'is_current' => 'boolean']; }

    protected static function booted(): void
    {
        static::saving(function (Term $term): void {
            if ($term->ends_on && $term->starts_on && $term->ends_on->lt($term->starts_on)) {
                throw ValidationException::withMessages(['ends_on' => 'The term must end after it starts.']);
            }
            if ($term->registration_ends_on && $term->registration_starts_on && $term->registration_ends_on->lt($term->registration_starts_on)) {
                throw ValidationException::withMessages(['registration_ends_on' => 'Registration must end after it starts.']);
            }
        });
    }

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function courseSections(): HasMany { return $this->hasMany(CourseSection::class); }
}
