<?php

namespace Tests\Feature;

use App\Models\FeeStructure;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class BillingFoundationTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_finance_officer_can_issue_invoice_and_duplicate_issue_is_reused(): void
    {
        $this->seed();
        $role = Role::where('slug', Role::FINANCE_OFFICER)->firstOrFail();
        $officer = User::factory()->create(['role_id' => $role->id]);
        $student = Student::firstOrFail();
        $structure = FeeStructure::firstOrFail();
        $payload = ['student_id' => $student->id, 'fee_structure_id' => $structure->id];

        $this->actingAs($officer)->post(route('fees.invoices.create'), $payload)->assertRedirect();
        $this->actingAs($officer)->post(route('fees.invoices.create'), $payload)->assertRedirect();
        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_items', 1);
    }

    public function test_student_can_only_view_their_own_invoice(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $other = Student::where('user_id', '!=', $student->id)->firstOrFail();
        $invoice = $other->invoices()->create([
            'invoice_number' => 'INV-TEST-'.uniqid(),
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
            'total' => 100,
            'balance' => 100,
        ]);

        $this->actingAs($student)->get(route('fees.show-invoice', $invoice))->assertForbidden();
    }
}
