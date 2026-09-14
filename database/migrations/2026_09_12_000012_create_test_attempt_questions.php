<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_attempt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained('test_attempts')->cascadeOnDelete();
            $table->foreignId('test_question_id')->nullable()->constrained('test_questions')->nullOnDelete();
            $table->text('question_text');
            $table->string('question_type');
            $table->unsignedInteger('marks');
            $table->unsignedInteger('order_index')->default(0);
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->timestamps();
            $table->unique(['test_attempt_id', 'order_index']);
            $table->index(['test_attempt_id', 'test_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempt_questions');
    }
};
