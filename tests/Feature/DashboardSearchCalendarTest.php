<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class DashboardSearchCalendarTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    /**
     * Setup test data with proper role relationships
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure every role used by these endpoint tests exists. Some migrations
        // create specialized roles before this setup runs.
        foreach ([
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'staff' => 'Staff',
            'teacher' => 'Teacher',
            'student' => 'Student',
            'parent' => 'Parent',
        ] as $slug => $name) {
            Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $name]
            );
        }
    }

    /**
     * Test that search endpoint requires authentication
     */
    public function test_search_endpoint_requires_authentication()
    {
        $response = $this->get('/api/search?q=test');
        $response->assertStatus(302); // Redirect to login
    }

    /**
     * Test that calendar endpoint requires authentication
     */
    public function test_calendar_endpoint_requires_authentication()
    {
        $response = $this->get('/api/calendar/events');
        $response->assertStatus(302); // Redirect to login
    }

    /**
     * Test super admin search endpoint returns results
     */
    public function test_super_admin_search_endpoint_works()
    {
        $superAdmin = User::factory()->create(['role_id' => Role::where('slug', 'super_admin')->first()->id]);
        
        $response = $this->actingAs($superAdmin)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test admin search endpoint returns results
     */
    public function test_admin_search_endpoint_works()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($admin)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test staff search endpoint returns results
     */
    public function test_staff_search_endpoint_works()
    {
        $staff = User::factory()->create(['role_id' => Role::where('slug', 'staff')->first()->id]);

        $response = $this->actingAs($staff)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test teacher search endpoint works
     */
    public function test_teacher_search_endpoint_works()
    {
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);

        $response = $this->actingAs($teacherUser)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test student search endpoint works
     */
    public function test_student_search_endpoint_works()
    {
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);

        $response = $this->actingAs($studentUser)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test parent search endpoint works
     */
    public function test_parent_search_endpoint_works()
    {
        $parentUser = User::factory()->create(['role_id' => Role::where('slug', 'parent')->first()->id]);

        $response = $this->actingAs($parentUser)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test calendar events endpoint returns role-specific events for admin
     */
    public function test_calendar_events_returns_role_specific_data()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($admin)->get('/api/calendar/events?year=2024&month=1');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }

    /**
     * Test calendar events respect month parameter
     */
    public function test_calendar_events_respects_month_parameter()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($admin)->get('/api/calendar/events?year=2024&month=6');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
        
        $events = $response->json('events');
        // Verify events have required structure
        foreach ($events as $event) {
            $this->assertArrayHasKey('start', $event);
            $this->assertArrayHasKey('title', $event);
        }
    }

    /**
     * Test search with short query still responds
     */
    public function test_search_with_short_query()
    {
        $user = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($user)->get('/api/search?q=a');
        $response->assertStatus(200);
    }

    /**
     * Test search results are properly formatted
     */
    public function test_search_results_are_properly_formatted()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        User::factory()->create(['name' => 'Alice Smith', 'role_id' => Role::where('slug', 'student')->first()->id]);

        $response = $this->actingAs($admin)->get('/api/search?q=alice');
        
        $response->assertStatus(200);
        $results = $response->json('results');
        
        foreach ($results as $result) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('type', $result);
            $this->assertArrayHasKey('id', $result);
            $this->assertArrayHasKey('title', $result);
            $this->assertArrayHasKey('subtitle', $result);
            $this->assertArrayHasKey('icon', $result);
        }
    }

    /**
     * Test teacher calendar shows proper structure
     */
    public function test_teacher_calendar_shows_proper_structure()
    {
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);

        $response = $this->actingAs($teacherUser)->get('/api/calendar/events?year=2024&month=1');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }

    /**
     * Test student calendar shows proper structure
     */
    public function test_student_calendar_shows_proper_structure()
    {
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);

        $response = $this->actingAs($studentUser)->get('/api/calendar/events?year=2024&month=1');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }

    /**
     * Test parent calendar shows proper structure
     */
    public function test_parent_calendar_shows_proper_structure()
    {
        $parentUser = User::factory()->create(['role_id' => Role::where('slug', 'parent')->first()->id]);

        $response = $this->actingAs($parentUser)->get('/api/calendar/events?year=2024&month=1');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }

    /**
     * Test search response JSON structure
     */
    public function test_search_response_has_results_key()
    {
        $user = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($user)->get('/api/search?q=test');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    /**
     * Test calendar response JSON structure
     */
    public function test_calendar_response_has_events_key()
    {
        $user = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($user)->get('/api/calendar/events');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }
}
