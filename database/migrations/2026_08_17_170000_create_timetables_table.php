<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('timetables')) {
            Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
                $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
                $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
                $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
                $table->time('start_time');
                $table->time('end_time');
                $table->string('room_number')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['school_class_id', 'day_of_week']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
