<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN         = 'super_admin';
    public const ADMIN              = 'admin';
    public const STAFF              = 'staff';
    public const TEACHER            = 'teacher';
    public const STUDENT            = 'student';
    public const PARENT             = 'parent';
    public const ADMISSIONS_OFFICER = 'admissions_officer';
    public const FINANCE_OFFICER    = 'finance_officer';
    public const REGISTRAR          = 'registrar';
    public const HR_OFFICER         = 'hr_officer';
    public const LIBRARIAN          = 'librarian';
    public const PRESIDENT          = 'president';
    public const CHANCELLOR         = 'chancellor';
    public const DEAN               = 'dean';
    public const DEPARTMENT_HEAD    = 'department_head';
    public const ACADEMIC_ADVISOR   = 'academic_advisor';

    protected $fillable = ['name', 'slug', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
