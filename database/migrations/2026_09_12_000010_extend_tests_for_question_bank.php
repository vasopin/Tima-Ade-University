<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->foreignId('test_id')->nullable()->change();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->index(['teacher_id', 'subject_id']);
        });

        Schema::create('test_question_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('test_question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->unsignedInteger('order_index')->default(0);
            $table->unsignedInteger('marks')->nullable();
            $table->timestamps();
            $table->unique(['test_id', 'test_question_id']);
            $table->index(['test_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_question_selections');
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['school_class_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['teacher_id', 'school_class_id', 'subject_id']);
            $table->dropIndex(['teacher_id', 'subject_id']);
            $table->foreignId('test_id')->nullable(false)->change();
        });
    }
};
