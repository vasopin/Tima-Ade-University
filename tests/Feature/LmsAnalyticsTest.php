<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class LmsAnalyticsTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_student_analytics_is_self_scoped(): void
    {
        $this->seed();
        $role = Role::where('slug', Role::STUDENT)->firstOrFail();
        $user = User::factory()->create(['role_id' => $role->id]);
        $class = SchoolClass::create(['name' => 'Analytics Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        Student::create(['user_id' => $user->id, 'roll_number' => 'AN-S', 'admission_number' => 'AN-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $this->actingAs($user)->get(route('student.analytics'))->assertOk()->assertSee('Learning Analytics');
    }

    public function test_teacher_analytics_denies_non_teachers(): void
    {
        $this->seed();
        $role = Role::where('slug', Role::STUDENT)->firstOrFail();
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user)->get(route('teacher.analytics'))->assertForbidden();
    }
}
