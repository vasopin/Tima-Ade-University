<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_mark_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_mark_id')->constrained()->cascadeOnDelete();
            $table->decimal('marks_obtained', 8, 2);
            $table->string('grade')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['exam_mark_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_mark_history');
    }
};
