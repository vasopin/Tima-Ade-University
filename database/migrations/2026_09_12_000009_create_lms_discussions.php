<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamps();
            $table->index(['teacher_id', 'status']);
            $table->index(['course_section_id', 'status']);
        });

        Schema::create('lms_discussion_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('lms_discussions')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lms_discussion_posts')->nullOnDelete();
            $table->text('body');
            $table->string('status')->default('visible');
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->index(['discussion_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_discussion_posts');
        Schema::dropIfExists('lms_discussions');
    }
};
