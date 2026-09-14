<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_work_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['support', 'maintenance']);
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'type', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('staff_work_items'); }
};