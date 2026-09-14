<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutExceptionHandling();
        $this->seed();
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
