<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rubric_criteria', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rubric_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('max_points', 8, 2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['rubric_id', 'sort_order']);
        });

        Schema::create('rubric_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rubric_criterion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('criterion_name');
            $table->decimal('criterion_max_points', 8, 2);
            $table->decimal('awarded_points', 8, 2);
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('graded_at');
            $table->timestamps();
            $table->index('assignment_submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubric_scores');
        Schema::dropIfExists('rubric_criteria');
        Schema::dropIfExists('rubrics');
    }
};
