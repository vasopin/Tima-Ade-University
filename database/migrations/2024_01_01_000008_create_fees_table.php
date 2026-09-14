<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('fee_type');      // e.g. Tuition, Transport, Library
            $table->decimal('amount', 10, 2);
            $table->string('academic_year');
            $table->string('term')->nullable(); // Term 1, 2, 3
            $table->date('due_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'online'])->default('cash');
            $table->enum('status', ['paid', 'partial', 'pending', 'overdue'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_structures');
    }
};
