<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curriculum extends Model
{
    protected $fillable = ['program_id', 'effective_academic_year_id', 'name', 'version', 'total_credits', 'status'];
    protected function casts(): array { return ['total_credits' => 'decimal:2']; }
    public function program(): BelongsTo { return $this->belongsTo(Program::class); }
    public function effectiveAcademicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class, 'effective_academic_year_id'); }
    public function courses(): BelongsToMany { return $this->belongsToMany(Subject::class, 'curriculum_courses', 'curriculum_id', 'course_id')->withPivot(['is_required', 'recommended_term', 'credits'])->withTimestamps(); }
}
