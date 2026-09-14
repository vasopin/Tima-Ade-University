<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NotificationPhaseTest extends TestCase
{
    public function test_notification_routes_exist_and_require_authentication(): void
    {
        $index = Route::getRoutes()->getByName('notifications.index');
        $markRead = Route::getRoutes()->getByName('notifications.read');
        $markAllRead = Route::getRoutes()->getByName('notifications.readAll');

        $this->assertNotNull($index);
        $this->assertNotNull($markRead);
        $this->assertNotNull($markAllRead);
        $this->assertContains('auth', $index->middleware());
        $this->assertContains('auth', $markRead->middleware());
        $this->assertContains('auth', $markAllRead->middleware());
    }
}
