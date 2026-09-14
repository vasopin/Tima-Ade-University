<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;

class FinanceOfficerAccessTest extends TestCase
{
    use ForceRefreshDatabase;

    private function createFinanceOfficer(): User
    {
        $role = Role::where('slug', 'finance_officer')->firstOrCreate([
            'name' => 'Finance Officer',
            'slug' => 'finance_officer',
        ]);
        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_finance_officer_can_access_dashboard(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();

        $response = $this->actingAs($officer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.finance-officer');
    }

    public function test_finance_officer_dashboard_shows_finance_data(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();

        $response = $this->actingAs($officer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('paymentsByStatus');
        $response->assertViewHas('recentPayments');
        $response->assertViewHas('outstandingBalances');
        $response->assertViewHas('feeByClass');
    }

    public function test_student_cannot_access_finance_officer_dashboard(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->first();

        $response = $this->actingAs($student)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.student');
    }

    public function test_teacher_cannot_access_finance_officer_dashboard(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->first();

        $response = $this->actingAs($teacher)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.teacher');
    }

    public function test_parent_cannot_access_finance_officer_dashboard(): void
    {
        $this->seed();
        $parent = User::where('email', 'parent1@school.com')->first();

        $response = $this->actingAs($parent)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.parent');
    }

    public function test_finance_officer_cannot_access_other_admin_functions(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();

        // Assuming /students is restricted to admin/staff
        $response = $this->actingAs($officer)->get('/students');
        $response->assertForbidden();
    }

    public function test_finance_officer_cannot_access_admissions_applications(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();

        $response = $this->actingAs($officer)->get('/admin/applications');
        $response->assertForbidden();
    }

    public function test_non_finance_roles_cannot_browse_fee_records(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();

        $this->actingAs($teacher)->get('/fees')->assertForbidden();
        $this->actingAs($teacher)->get('/fees/structures')->assertForbidden();
    }

    public function test_finance_officer_can_record_a_payment(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();
        $student = Student::firstOrFail();
        $structure = FeeStructure::firstOrFail();

        $this->actingAs($officer)->post('/fees/store', [
            'student_id' => $student->id,
            'fee_structure_id' => $structure->id,
            'amount_paid' => $structure->amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ])->assertRedirect('/fees');

        $this->assertDatabaseHas('fee_payments', [
            'student_id' => $student->id,
            'fee_structure_id' => $structure->id,
            'status' => 'paid',
        ]);
    }

    public function test_fee_discount_cannot_exceed_amount_due(): void
    {
        $this->seed();
        $officer = $this->createFinanceOfficer();
        $student = Student::firstOrFail();
        $structure = FeeStructure::firstOrFail();

        $this->actingAs($officer)->from('/fees/create')->post('/fees/store', [
            'student_id' => $student->id,
            'fee_structure_id' => $structure->id,
            'amount_paid' => 0,
            'discount' => $structure->amount + 1,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ])->assertStatus(422);
    }
}
