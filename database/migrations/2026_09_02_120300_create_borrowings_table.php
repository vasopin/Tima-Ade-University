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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_member_id')->constrained('library_members')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue', 'lost'])->default('active');
            $table->boolean('is_renewed')->default(false);
            $table->timestamps();
            $table->index(['library_member_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
