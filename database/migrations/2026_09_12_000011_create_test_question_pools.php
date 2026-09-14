<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_question_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('question_type')->nullable();
            $table->unsignedInteger('questions_count');
            $table->unsignedInteger('marks_per_question')->nullable();
            $table->timestamps();
            $table->index(['test_id', 'question_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_question_pools');
    }
};
