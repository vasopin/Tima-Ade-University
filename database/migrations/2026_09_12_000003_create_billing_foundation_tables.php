<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('issued');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discounts', 12, 2)->default(0);
            $table->decimal('scholarship_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
            $table->index(['student_id', 'status']);
            $table->index(['term_id', 'status']);
        });

        Schema::create('invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fee_type');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('scholarship_awards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'status']);
        });

        Schema::create('refunds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fee_payment_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('reason');
            $table->string('status')->default('requested');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            $table->index(['fee_payment_id', 'status']);
        });

        Schema::table('fee_payments', function (Blueprint $table): void {
            $table->foreignId('invoice_id')->nullable()->after('fee_structure_id')->constrained()->nullOnDelete();
            $table->string('transaction_reference')->nullable()->unique()->after('receipt_number');
            $table->string('idempotency_key')->nullable()->unique()->after('transaction_reference');
            $table->dateTime('reconciled_at')->nullable()->after('received_by');
            $table->foreignId('reconciled_by')->nullable()->after('reconciled_at')->constrained('users')->nullOnDelete();
            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table): void {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['reconciled_by']);
            $table->dropIndex(['invoice_id', 'status']);
            $table->dropUnique(['transaction_reference']);
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn(['invoice_id', 'transaction_reference', 'idempotency_key', 'reconciled_at', 'reconciled_by']);
        });
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('scholarship_awards');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
