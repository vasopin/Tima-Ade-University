<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE live_classes MODIFY status ENUM('created', 'scheduled', 'live', 'ended') NOT NULL DEFAULT 'created'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE live_classes MODIFY status ENUM('scheduled', 'live', 'ended') NOT NULL DEFAULT 'scheduled'");
        }
    }
};