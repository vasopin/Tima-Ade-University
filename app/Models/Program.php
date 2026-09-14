<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = ['department_id', 'name', 'code', 'degree_type', 'duration_years', 'total_credits', 'description', 'is_active'];

    protected function casts(): array { return ['is_active' => 'boolean', 'total_credits' => 'decimal:2']; }

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function students(): HasMany { return $this->hasMany(Student::class); }
    public function curricula(): HasMany { return $this->hasMany(Curriculum::class); }
}
