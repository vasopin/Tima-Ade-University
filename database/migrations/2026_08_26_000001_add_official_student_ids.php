<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_id_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('next_number')->default(0);
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id', 15)->nullable()->unique()->after('id');
        });

        $sequences = [];
        DB::table('students')->orderBy('id')->get()->each(function ($student) use (&$sequences): void {
            $year = $student->admission_date
                ? (int) substr($student->admission_date, 0, 4)
                : (int) substr($student->created_at, 0, 4);
            $year = $year > 0 ? $year : (int) date('Y');
            $sequences[$year] = ($sequences[$year] ?? 0) + 1;

            DB::table('students')->where('id', $student->id)->update([
                'student_id' => sprintf('TAU-%d-%06d', $year, $sequences[$year]),
            ]);
        });

        foreach ($sequences as $year => $nextNumber) {
            DB::table('student_id_sequences')->insert([
                'year' => $year,
                'next_number' => $nextNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['student_id']);
            $table->dropColumn('student_id');
        });

        Schema::dropIfExists('student_id_sequences');
    }
};
