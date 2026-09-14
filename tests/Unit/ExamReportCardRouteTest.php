<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExamReportCardRouteTest extends TestCase
{
    public function test_report_card_route_exists_and_requires_auth()
    {
        $route = Route::getRoutes()->getByName('exams.report-card');
        $this->assertNotNull($route);
        $this->assertContains('auth', $route->middleware());
        $this->assertStringContainsString('reportCard', $route->getActionName());
    }
}
