<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->enum('grade', ['A+','A','B+','B','C+','C','D','F'])->nullable();
            $table->boolean('is_absent')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
