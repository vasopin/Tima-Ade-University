<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->foreignId('course_section_id')->nullable()->after('subject_id')->constrained('course_sections')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->after('course_section_id')->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('venue')->nullable()->after('duration_minutes');
            $table->string('workflow_status')->default('draft')->after('status');
            $table->foreignId('submitted_by')->nullable()->after('workflow_status')->constrained('users')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable()->after('submitted_by');
            $table->dateTime('published_at')->nullable()->after('submitted_at');
            $table->index(['course_section_id', 'term_id', 'workflow_status']);
        });

        Schema::table('exam_marks', function (Blueprint $table): void {
            $table->foreignId('recorded_by')->nullable()->after('remarks')->constrained('users')->nullOnDelete();
            $table->string('change_reason')->nullable()->after('recorded_by');
            $table->dateTime('submitted_at')->nullable()->after('change_reason');
            $table->index(['student_id', 'exam_id']);
        });

        Schema::table('exam_results_approval', function (Blueprint $table): void {
            $table->dateTime('published_at')->nullable()->after('approved_at');
            $table->foreignId('published_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exam_results_approval', function (Blueprint $table): void {
            $table->dropForeign(['published_by']);
            $table->dropColumn(['published_at', 'published_by']);
        });
        Schema::table('exam_marks', function (Blueprint $table): void {
            $table->dropForeign(['recorded_by']);
            $table->dropIndex(['student_id', 'exam_id']);
            $table->dropColumn(['recorded_by', 'change_reason', 'submitted_at']);
        });
        Schema::table('exams', function (Blueprint $table): void {
            foreach (['course_section_id', 'academic_year_id', 'term_id', 'submitted_by'] as $foreign) {
                $table->dropForeign([$foreign]);
            }
            $table->dropIndex(['course_section_id', 'term_id', 'workflow_status']);
            $table->dropColumn(['course_section_id', 'academic_year_id', 'term_id', 'end_time', 'venue', 'workflow_status', 'submitted_by', 'submitted_at', 'published_at']);
        });
    }
};
