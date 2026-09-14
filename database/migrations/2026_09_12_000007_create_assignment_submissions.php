<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table): void {
            $table->dateTime('available_from')->nullable()->after('due_date');
            $table->unsignedInteger('max_attempts')->default(1)->after('available_from');
            $table->decimal('max_points', 8, 2)->default(100)->after('max_attempts');
            $table->dateTime('published_at')->nullable()->after('status');
            $table->index(['school_class_id', 'subject_id', 'status']);
        });

        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attempt_number');
            $table->string('status')->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->decimal('score', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id', 'attempt_number']);
            $table->index(['assignment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::table('assignments', function (Blueprint $table): void {
            $table->dropIndex(['school_class_id', 'subject_id', 'status']);
            $table->dropColumn(['available_from', 'max_attempts', 'max_points', 'published_at']);
        });
    }
};
