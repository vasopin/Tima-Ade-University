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
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->string('term')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->integer('total_credits')->default(0);
            $table->text('summary')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
