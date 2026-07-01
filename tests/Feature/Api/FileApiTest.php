<?php

namespace Tests\Feature\Api;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_user_can_upload_file_via_api(): void
    {
        $user = User::where('email', 'user@laravel-cdn.com')->first();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('report.txt', 10, 'text/plain');

        $response = $this->postJson('/api/files/upload', [
            'files' => [$file],
            'folder' => 'docs',
            'visibility' => 'public',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('files', [
            'user_id' => $user->id,
            'folder' => 'docs',
            'is_public' => true,
        ]);
    }

    public function test_user_can_update_own_file(): void
    {
        $user = User::where('email', 'user@laravel-cdn.com')->first();
        $file = File::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'Old Name',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/files/{$file->id}", [
            'display_name' => 'New Name',
            'folder' => 'updated',
            'is_public' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.display_name', 'New Name');

        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'display_name' => 'New Name',
            'folder' => 'updated',
            'is_public' => false,
        ]);
    }

    public function test_user_can_delete_own_file(): void
    {
        $user = User::where('email', 'user@laravel-cdn.com')->first();
        Storage::disk('public')->put('files/test.txt', 'content');
        $file = File::factory()->create([
            'user_id' => $user->id,
            'path' => 'files/test.txt',
            'disk' => 'public',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/files/{$file->id}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    public function test_user_cannot_view_other_users_file(): void
    {
        $owner = User::where('email', 'user@laravel-cdn.com')->first();
        $other = User::factory()->create();
        $other->assignRole('user');

        $file = File::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($other);

        $this->getJson("/api/files/{$file->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_dedup_is_scoped_per_user(): void
    {
        $userA = User::where('email', 'user@laravel-cdn.com')->first();
        $userB = User::factory()->create();
        $userB->assignRole('user');

        $content = UploadedFile::fake()->create('shared.txt', 10, 'text/plain');

        Sanctum::actingAs($userA);
        $first = $this->postJson('/api/files/upload', [
            'files' => [$content],
            'visibility' => 'public',
        ])->assertCreated();

        $fileIdA = $first->json('data.0.id');

        Sanctum::actingAs($userB);
        $second = $this->postJson('/api/files/upload', [
            'files' => [UploadedFile::fake()->createWithContent('shared.txt', file_get_contents($content->getPathname()))],
            'visibility' => 'public',
        ])->assertCreated();

        $fileIdB = $second->json('data.0.id');

        $this->assertNotEquals($fileIdA, $fileIdB);

        Sanctum::actingAs($userA);
        $duplicate = $this->postJson('/api/files/upload', [
            'files' => [UploadedFile::fake()->createWithContent('shared.txt', file_get_contents($content->getPathname()))],
            'visibility' => 'public',
        ])->assertCreated();

        $this->assertEquals($fileIdA, $duplicate->json('data.0.id'));
    }

    public function test_stats_endpoint_resolves_before_resource_show(): void
    {
        $admin = User::where('email', 'admin@laravel-cdn.com')->first();
        Sanctum::actingAs($admin);

        $this->getJson('/api/files/stats')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data']);
    }

    public function test_api_register_is_disabled_by_default(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'API User',
            'email' => 'api@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
