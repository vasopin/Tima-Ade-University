<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_standing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->onDelete('cascade');
            $table->enum('status', ['good', 'probation', 'suspended', 'dismissed'])->default('good');
            $table->decimal('cumulative_gpa', 3, 2)->default(0);
            $table->integer('failed_courses')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_standing');
    }
};
