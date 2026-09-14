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
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_member_id')->constrained('library_members')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->date('reservation_date');
            $table->enum('status', ['active', 'fulfilled', 'cancelled'])->default('active');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
            $table->index(['library_member_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reservations');
    }
};
