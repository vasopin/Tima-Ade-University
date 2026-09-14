<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade_level')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('name');
            $table->integer('capacity')->default(40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
        Schema::dropIfExists('school_classes');
    }
};
