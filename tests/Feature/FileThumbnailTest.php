<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileThumbnailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_public_file_thumbnail_is_served_via_route(): void
    {
        $user = User::where('email', 'user@laravel-cdn.com')->first();
        $path = 'files/photo.jpg';
        $thumbPath = 'thumbnails/photo_thumb.jpg';

        Storage::disk('public')->put($path, 'original');
        Storage::disk('public')->put($thumbPath, 'thumbnail-bytes');

        $file = File::factory()->create([
            'user_id' => $user->id,
            'path' => $path,
            'disk' => 'public',
            'is_public' => true,
            'thumbnail_path' => $thumbPath,
            'mime_type' => 'image/jpeg',
        ]);

        $response = $this->get(route('file.thumbnail', $file->slug));

        $response->assertOk();
        $this->assertSame('thumbnail-bytes', $response->streamedContent());
    }

    public function test_public_file_view_route_serves_inline_content(): void
    {
        $user = User::where('email', 'user@laravel-cdn.com')->first();
        $path = 'files/photo.jpg';

        Storage::disk('public')->put($path, 'image-bytes');

        $file = File::factory()->create([
            'user_id' => $user->id,
            'path' => $path,
            'disk' => 'public',
            'is_public' => true,
            'mime_type' => 'image/jpeg',
        ]);

        $response = $this->get(route('file.view', $file->slug));

        $response->assertOk();
        $this->assertSame('image-bytes', $response->streamedContent());
        $this->assertSame(route('file.view', $file->slug), $file->url);
    }

    public function test_private_file_thumbnail_requires_owner_session(): void
    {
        $owner = User::where('email', 'user@laravel-cdn.com')->first();
        $other = User::where('email', 'admin@laravel-cdn.com')->first();
        $thumbPath = 'thumbnails/private_thumb.jpg';

        Storage::disk('local')->put('files/private.jpg', 'original');
        Storage::disk('local')->put($thumbPath, 'private-thumb');

        $file = File::factory()->create([
            'user_id' => $owner->id,
            'path' => 'files/private.jpg',
            'disk' => 'local',
            'is_public' => false,
            'thumbnail_path' => $thumbPath,
            'mime_type' => 'image/jpeg',
        ]);

        $this->get(route('file.thumbnail', $file->slug))->assertForbidden();

        $this->actingAs($owner)
            ->get(route('file.thumbnail', $file->slug))
            ->assertOk();

        $this->actingAs($other)
            ->get(route('file.thumbnail', $file->slug))
            ->assertOk();
    }
}
