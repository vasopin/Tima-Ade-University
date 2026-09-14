<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->string('status')->default('published')->after('is_active');
            $table->string('audience')->default('everyone')->after('status');
            $table->date('expires_at')->nullable()->after('published_date');
            $table->string('video_path')->nullable()->after('expires_at');
            $table->string('video_original_name')->nullable()->after('video_path');
            $table->string('video_mime_type')->nullable()->after('video_original_name');
            $table->unsignedBigInteger('video_file_size')->nullable()->after('video_mime_type');
            $table->string('thumbnail_path')->nullable()->after('video_file_size');
            $table->string('attachment_path')->nullable()->after('thumbnail_path');
            $table->string('external_video_url')->nullable()->after('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'audience', 'expires_at', 'video_path', 'video_original_name',
                'video_mime_type', 'video_file_size', 'thumbnail_path', 'attachment_path',
                'external_video_url',
            ]);
        });
    }
};
