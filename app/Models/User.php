<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id', 'faculty_id', 'name', 'email', 'password', 'phone', 'avatar', 'is_active', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'status'            => 'string',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class, 'user_id');
    }

    /**
     * Alias for guardian() — keep backward/forward compatibility with "parent" naming.
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Guardian::class, 'user_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function advisorAssignments(): HasMany
    {
        return $this->hasMany(AdvisorAssignment::class, 'advisor_id');
    }

    public function courseSections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'teacher_id');
    }

    public function advisingAppointments(): HasMany
    {
        return $this->hasMany(AdvisingAppointment::class, 'advisor_id');
    }

    public function advisingNotes(): HasMany
    {
        return $this->hasMany(AdvisingNote::class, 'advisor_id');
    }

    public function libraryMember(): HasOne
    {
        return $this->hasOne(LibraryMember::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === Role::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role?->slug, [Role::ADMIN, Role::SUPER_ADMIN]);
    }

    public function isStaff(): bool
    {
        return $this->role?->slug === Role::STAFF;
    }

    public function isTeacher(): bool
    {
        return $this->role?->slug === Role::TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role?->slug === Role::STUDENT;
    }

    public function isParent(): bool
    {
        return $this->role?->slug === Role::PARENT;
    }

    public function isAdmissionsOfficer(): bool
    {
        return $this->role?->slug === Role::ADMISSIONS_OFFICER;
    }

    public function isFinanceOfficer(): bool
    {
        return $this->role?->slug === Role::FINANCE_OFFICER;
    }

    public function isRegistrar(): bool
    {
        return $this->role?->slug === Role::REGISTRAR;
    }

    public function isHROfficer(): bool
    {
        return $this->role?->slug === Role::HR_OFFICER;
    }

    public function isLibrarian(): bool
    {
        return $this->role?->slug === Role::LIBRARIAN;
    }

    public function isPresident(): bool
    {
        return $this->role?->slug === Role::PRESIDENT;
    }

    public function isChancellor(): bool
    {
        return $this->role?->slug === Role::CHANCELLOR;
    }

    public function isExecutive(): bool
    {
        return $this->isPresident() || $this->isChancellor();
    }

    public function isDean(): bool
    {
        return $this->role?->slug === Role::DEAN;
    }

    public function headedDepartment(): HasOne
    {
        return $this->hasOne(Department::class, 'head_user_id');
    }

    public function isDepartmentHead(): bool
    {
        return $this->role?->slug === Role::DEPARTMENT_HEAD;
    }

    public function isAcademicAdvisor(): bool
    {
        return $this->role?->slug === Role::ACADEMIC_ADVISOR;
    }

    public function canLogin(): bool
    {
        return $this->is_active && ($this->status === 'active' || is_null($this->status));
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => '<span class="badge bg-success">Active</span>',
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            'inactive'  => '<span class="badge bg-secondary">Inactive</span>',
            'suspended' => '<span class="badge bg-danger">Suspended</span>',
            default     => '<span class="badge bg-success">Active</span>',
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Ensure color code uses Tima-Ade University blue (#016ED5) via hex without '#'
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=016ED5&color=fff&size=128';
    }
}
