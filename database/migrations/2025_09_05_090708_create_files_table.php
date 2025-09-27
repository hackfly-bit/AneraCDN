<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Original filename
            $table->string('display_name')->nullable(); // Custom display name
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->string('path'); // Storage path
            $table->string('disk')->default('public'); // Storage disk
            $table->string('mime_type');
            $table->string('extension');
            $table->bigInteger('size'); // File size in bytes
            $table->string('hash')->unique(); // File hash for deduplication
            $table->json('metadata')->nullable(); // Additional metadata (dimensions, duration, etc.)
            $table->boolean('is_public')->default(true);
            $table->boolean('is_optimized')->default(false);
            $table->string('thumbnail_path')->nullable();
            $table->string('webp_path')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('folder')->nullable(); // Folder organization
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'folder']);
            $table->index(['mime_type']);
            $table->index(['is_public']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
