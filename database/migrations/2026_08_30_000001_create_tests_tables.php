<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tests table - core test metadata
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->integer('total_marks')->default(100);
            $table->integer('pass_marks')->default(40);
            $table->integer('duration_minutes')->nullable();
            $table->integer('attempt_limit')->default(1);
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('close_date')->nullable();
            $table->timestamp('results_release_date')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index('teacher_id');
            $table->index('school_class_id');
            $table->index('subject_id');
            $table->index('status');
            $table->index('publish_date');
        });

        // Test Questions table
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer'])->default('multiple_choice');
            $table->integer('marks')->default(1);
            $table->integer('order_index')->default(0);
            $table->text('correct_answer')->nullable(); // For true_false and short_answer
            $table->timestamps();

            $table->index('test_id');
            $table->index('order_index');
        });

        // Test Options table - for MCQ and True/False questions
        Schema::create('test_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->index('test_question_id');
            $table->index('order_index');
        });

        // Test Attempts table - tracks when a student attempts a test
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_minutes')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'results_released'])->default('in_progress');
            $table->decimal('score', 8, 2)->nullable();
            $table->string('grade')->nullable();
            $table->boolean('is_passed')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['test_id', 'student_id', 'attempt_number']);
            $table->index('test_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('submitted_at');
        });

        // Test Answers table - student's answer to each question
        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained('test_attempts')->cascadeOnDelete();
            $table->foreignId('test_question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable(); // For short answer questions
            $table->foreignId('selected_option_id')->nullable()->constrained('test_options')->cascadeOnDelete(); // For MCQ/True-False
            $table->boolean('is_correct')->nullable(); // null = not yet graded, true/false = graded
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();

            $table->index('test_attempt_id');
            $table->index('test_question_id');
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('test_options');
        Schema::dropIfExists('test_questions');
        Schema::dropIfExists('tests');
    }
};
