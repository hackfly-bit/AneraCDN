<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup permissions and roles for all tests
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_user_can_access_dashboard_when_authenticated(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }

    public function test_dashboard_files_page_loads(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard/files');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.files');
    }

    public function test_dashboard_upload_page_loads(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard/upload');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.upload');
    }

    public function test_dashboard_stats_page_loads(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard/stats');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.stats');
    }

    public function test_dashboard_activities_page_loads(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard/activities');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.activities');
    }

    public function test_dashboard_navigation_links_are_present(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Files');
        $response->assertSee('Upload');
        $response->assertSee('Statistics');
        $response->assertSee('Activities');
    }
}
