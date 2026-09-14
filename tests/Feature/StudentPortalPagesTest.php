<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class StudentPortalPagesTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_student_portal_pages_render_for_the_authenticated_student(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->firstOrFail();

        foreach ([
            'student/registration' => 'student.registration',
            'student/schedule' => 'student.schedule',
            'student/academic-records' => 'student.academic-records',
            'student/fees' => 'student.fees',
            'student/library' => 'student.library',
        ] as $uri => $view) {
            $this->actingAs($student)->get($uri)->assertOk()->assertViewIs($view);
        }
    }

    public function test_non_students_cannot_access_student_portal_pages(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();

        $this->actingAs($teacher)->get('/student/registration')->assertForbidden();
        $this->actingAs($teacher)->get('/student/library')->assertForbidden();
    }
}
