<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('address')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->boolean('is_class_teacher')->default(false);
            $table->foreignId('class_teacher_of')->nullable()->constrained('school_classes')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
