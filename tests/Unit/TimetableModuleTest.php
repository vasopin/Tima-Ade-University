<?php

namespace Tests\Unit;

use Tests\TestCase;

class TimetableModuleTest extends TestCase
{
    public function test_timetable_migration_model_controller_and_views_exist()
    {
        $this->assertFileExists(base_path('database/migrations/2026_08_17_170000_create_timetables_table.php'));
        $this->assertTrue(class_exists(\App\Models\Timetable::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\TimetableController::class));

        $this->assertFileExists(base_path('resources/views/timetables/index.blade.php'));
        $this->assertFileExists(base_path('resources/views/timetables/create.blade.php'));
        $this->assertFileExists(base_path('resources/views/timetables/edit.blade.php'));

        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('timetables.index');
        $this->assertNotNull($route);
        $this->assertContains('auth', $route->middleware());
    }
}
