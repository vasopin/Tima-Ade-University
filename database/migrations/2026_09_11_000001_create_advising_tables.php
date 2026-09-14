<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('assigned_at');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'is_active']);
            $table->index(['advisor_id', 'is_active']);
        });

        Schema::create('advising_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('mode', 30)->default('in_person');
            $table->string('status', 30)->default('requested');
            $table->string('topic')->nullable();
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();

            $table->index(['advisor_id', 'scheduled_at']);
            $table->index(['student_id', 'scheduled_at']);
        });

        Schema::create('advising_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_private')->default(false);
            $table->date('follow_up_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advising_notes');
        Schema::dropIfExists('advising_appointments');
        Schema::dropIfExists('advisor_assignments');
    }
};
