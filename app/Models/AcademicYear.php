<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'code', 'starts_on', 'ends_on', 'status', 'is_current'];

    protected function casts(): array { return ['starts_on' => 'date', 'ends_on' => 'date', 'is_current' => 'boolean']; }

    protected static function booted(): void
    {
        static::saving(function (AcademicYear $year): void {
            if ($year->ends_on && $year->starts_on && $year->ends_on->lt($year->starts_on)) {
                throw ValidationException::withMessages(['ends_on' => 'The academic year must end after it starts.']);
            }
            if ($year->is_current) {
                static::whereKeyNot($year->getKey())->update(['is_current' => false]);
            }
        });
    }

    public function terms(): HasMany { return $this->hasMany(Term::class); }
}
