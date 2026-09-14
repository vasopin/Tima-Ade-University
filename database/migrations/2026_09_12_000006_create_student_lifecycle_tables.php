<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->string('lifecycle_status')->nullable()->after('status');
            $table->index('lifecycle_status');
        });

        Schema::create('student_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['student_id', 'effective_date']);
        });

        Schema::create('student_program_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('to_program_id')->constrained('programs')->restrictOnDelete();
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['student_id', 'effective_date']);
        });

        Schema::create('student_transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('to_program_id')->constrained('programs')->restrictOnDelete();
            $table->string('type')->default('program');
            $table->string('status')->default('requested');
            $table->date('effective_date')->nullable();
            $table->text('reason');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'status']);
        });

        Schema::create('student_holds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('reason');
            $table->string('status')->default('active');
            $table->date('effective_date');
            $table->date('release_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_holds');
        Schema::dropIfExists('student_transfers');
        Schema::dropIfExists('student_program_histories');
        Schema::dropIfExists('student_status_histories');
        Schema::table('students', function (Blueprint $table): void {
            $table->dropIndex(['lifecycle_status']);
            $table->dropColumn('lifecycle_status');
        });
    }
};
