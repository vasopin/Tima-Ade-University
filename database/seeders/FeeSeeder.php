<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        $classes    = DB::table('school_classes')->get();
        $adminId    = DB::table('users')->where('email', 'admin@school.com')->value('id');
        $students   = DB::table('students')->get();

        // Create fee structures for each class
        $feeTypes = [
            ['fee_type' => 'Tuition Fee',    'amount' => 1500.00],
            ['fee_type' => 'Library Fee',    'amount' => 100.00],
            ['fee_type' => 'Transport Fee',  'amount' => 300.00],
            ['fee_type' => 'Laboratory Fee', 'amount' => 200.00],
        ];

        $feeStructureIds = [];
        foreach ($classes as $class) {
            foreach ($feeTypes as $feeType) {
                $id = DB::table('fee_structures')->insertGetId(array_merge($feeType, [
                    'school_class_id' => $class->id,
                    'academic_year'   => '2024-2025',
                    'term'            => 'Term 1',
                    'due_date'        => '2024-09-30',
                    'is_active'       => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]));
                $feeStructureIds[$class->id][] = $id;
            }
        }

        // Create sample payments for each student
        $receiptCounter = 1001;
        foreach ($students as $student) {
            $classIds = $feeStructureIds[$student->school_class_id] ?? [];
            foreach ($classIds as $index => $feeStructureId) {
                $isPaid = $index < 2; // First 2 fees paid, rest pending
                DB::table('fee_payments')->insert([
                    'student_id'        => $student->id,
                    'fee_structure_id'  => $feeStructureId,
                    'receipt_number'    => 'RCPT-' . $receiptCounter++,
                    'amount_paid'       => $isPaid ? DB::table('fee_structures')->where('id', $feeStructureId)->value('amount') : 0,
                    'discount'          => 0,
                    'late_fee'          => 0,
                    'payment_date'      => $isPaid ? '2024-09-05' : now()->toDateString(),
                    'payment_method'    => 'cash',
                    'status'            => $isPaid ? 'paid' : 'pending',
                    'received_by'       => $adminId,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }
    }
}
