<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['campus_id', 'code']);
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['faculty_id', 'code']);
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('degree_type');
            $table->unsignedSmallInteger('duration_years')->nullable();
            $table->decimal('total_credits', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status')->default('open');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->index(['is_current', 'status']);
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->date('registration_starts_on')->nullable();
            $table->date('registration_ends_on')->nullable();
            $table->string('status')->default('open');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'code']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('credits', 6, 2)->nullable();
            $table->string('level')->nullable();
            $table->string('course_type')->default('core');
            $table->index(['department_id', 'is_active']);
        });

        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('prerequisite_course_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('minimum_grade')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'prerequisite_course_id']);
        });

        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('effective_academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('name');
            $table->string('version');
            $table->decimal('total_credits', 8, 2)->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['program_id', 'version']);
        });

        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('subjects')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->unsignedTinyInteger('recommended_term')->nullable();
            $table->decimal('credits', 6, 2)->nullable();
            $table->timestamps();
            $table->unique(['curriculum_id', 'course_id']);
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code');
            $table->string('room')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->unique(['course_id', 'term_id', 'code']);
            $table->index(['teacher_id', 'term_id']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->dateTime('enrolled_at');
            $table->string('status')->default('enrolled');
            $table->string('final_grade')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_section_id', 'status']);
            $table->index(['student_id', 'status']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', fn (Blueprint $table) => $table->dropForeign(['program_id']));
        Schema::table('students', fn (Blueprint $table) => $table->dropColumn('program_id'));
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('curriculum_courses');
        Schema::dropIfExists('curricula');
        Schema::dropIfExists('course_prerequisites');
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'credits', 'level', 'course_type']);
        });
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('campuses');
    }
};
