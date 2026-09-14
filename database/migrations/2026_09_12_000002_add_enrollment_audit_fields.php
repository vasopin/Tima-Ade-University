<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table): void {
            $table->foreignId('action_by')->nullable()->after('final_grade')->constrained('users')->nullOnDelete();
            $table->dateTime('status_changed_at')->nullable()->after('action_by');
            $table->text('action_reason')->nullable()->after('status_changed_at');
            $table->index(['course_section_id', 'status']);
            $table->index(['status', 'status_changed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table): void {
            $table->dropForeign(['action_by']);
            $table->dropIndex(['course_section_id', 'status']);
            $table->dropIndex(['status', 'status_changed_at']);
            $table->dropColumn(['action_by', 'status_changed_at', 'action_reason']);
        });
    }
};
