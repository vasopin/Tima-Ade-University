<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->integer('duration_seconds')->default(0);
            $table->string('mime_type')->default('audio/webm');
            $table->integer('file_size')->default(0);
            $table->timestamp('played_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'played_at']);
        });

        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, ringing, connected, ended, declined, missed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->string('call_sid')->nullable(); // external call ID from provider
            $table->timestamps();
            $table->index(['recipient_id', 'status']);
            $table->index(['caller_id', 'created_at']);
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('call_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // initiated, received, accepted, declined, ended
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_history');
        Schema::dropIfExists('calls');
        Schema::dropIfExists('voice_messages');
    }
};
