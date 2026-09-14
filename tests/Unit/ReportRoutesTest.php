<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReportRoutesTest extends TestCase
{
    public function test_report_export_routes_exist_and_require_auth()
    {
        $attRoute = Route::getRoutes()->getByName('reports.attendance.export');
        $feesRoute = Route::getRoutes()->getByName('reports.fees.export');

        $this->assertNotNull($attRoute);
        $this->assertNotNull($feesRoute);

        $this->assertContains('auth', $attRoute->middleware());
        $this->assertContains('auth', $feesRoute->middleware());

        $this->assertStringContainsString('exportAttendanceCsv', $attRoute->getActionName());
        $this->assertStringContainsString('exportFeesCsv', $feesRoute->getActionName());
    }
}
