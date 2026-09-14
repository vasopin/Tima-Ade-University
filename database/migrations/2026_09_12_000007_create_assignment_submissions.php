<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table): void {
            if (! Schema::hasColumn('assignments', 'available_from')) {
                $table->dateTime('available_from')->nullable()->after('due_date');
            }
            if (! Schema::hasColumn('assignments', 'max_attempts')) {
                $table->unsignedInteger('max_attempts')->default(1)->after('available_from');
            }
            if (! Schema::hasColumn('assignments', 'max_points')) {
                $table->decimal('max_points', 8, 2)->default(100)->after('max_attempts');
            }
            if (! Schema::hasColumn('assignments', 'published_at')) {
                $table->dateTime('published_at')->nullable()->after('status');
            }

            $indexes = collect(Schema::getIndexes('assignments'))
                ->pluck('name')
                ->all();
            if (! in_array('assignments_school_class_id_subject_id_status_index', $indexes, true)) {
                $table->index(['school_class_id', 'subject_id', 'status']);
            }
        });

        if (! Schema::hasTable('assignment_submissions')) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::table('assignments', function (Blueprint $table): void {
            $indexes = collect(Schema::getIndexes('assignments'))
                ->pluck('name')
                ->all();
            if (in_array('assignments_school_class_id_subject_id_status_index', $indexes, true)) {
                $table->dropIndex(['school_class_id', 'subject_id', 'status']);
            }

            $columns = ['available_from', 'max_attempts', 'max_points', 'published_at'];
            $existingColumns = array_values(array_filter(
                $columns,
                static fn (string $column): bool => Schema::hasColumn('assignments', $column),
            ));
            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
