<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('grade_interested');
            $table->text('message')->nullable();
            $table->json('education')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'declined'])->default('submitted');
            $table->text('decision_reason')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
