<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(45);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->enum('status', ['created', 'scheduled', 'live', 'ended'])->default('created');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('live_class_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained('live_classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['teacher', 'student'])->default('student');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_muted')->default(false);
            $table->boolean('camera_on')->default(true);
            $table->boolean('screen_sharing')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->foreignId('removed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('live_class_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained('live_classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_name');
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('live_class_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained('live_classes')->onDelete('cascade');
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_class_activities');
        Schema::dropIfExists('live_class_messages');
        Schema::dropIfExists('live_class_participants');
        Schema::dropIfExists('live_classes');
    }
};
