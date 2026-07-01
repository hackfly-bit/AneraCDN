<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FileService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * Upload and store a file
     */
    public function uploadFile(UploadedFile $uploadedFile, $userId, $folder = null, $isPublic = true)
    {
        // Generate unique filename
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueFilename = Str::slug($filename).'_'.time().'.'.$extension;

        // Determine storage path
        $storagePath = $folder ? "files/{$folder}/{$uniqueFilename}" : "files/{$uniqueFilename}";

        // Store file
        $disk = $isPublic ? 'public' : 'local';
        $path = $uploadedFile->storeAs(dirname($storagePath), basename($storagePath), $disk);

        // Calculate file hash for deduplication
        $hash = hash_file('sha256', $uploadedFile->getPathname());

        // Check if file already exists for this user
        $existingFile = File::where('hash', $hash)->where('user_id', $userId)->first();
        if ($existingFile) {
            // Delete the newly uploaded file since we have a duplicate
            Storage::disk($disk)->delete($path);

            // Log activity
            $this->logActivity($existingFile->id, $userId, FileActivity::ACTION_UPLOAD, [
                'duplicate' => true,
                'original_name' => $originalName,
            ]);

            return $existingFile;
        }

        // Get file metadata
        $metadata = $this->getFileMetadata($uploadedFile);

        // Create file record
        $file = File::create([
            'name' => $originalName,
            'display_name' => $filename,
            'slug' => Str::uuid(),
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $uploadedFile->getMimeType(),
            'extension' => $extension,
            'size' => $uploadedFile->getSize(),
            'hash' => $hash,
            'metadata' => $metadata,
            'is_public' => $isPublic,
            'user_id' => $userId,
            'folder' => $folder,
        ]);

        // Generate thumbnail for images
        if ($this->isImage($uploadedFile->getMimeType())) {
            $this->generateThumbnail($file);
            $this->generateWebP($file);
        }

        // Log activity
        $this->logActivity($file->id, $userId, FileActivity::ACTION_UPLOAD, [
            'original_name' => $originalName,
            'size' => $uploadedFile->getSize(),
        ]);

        return $file;
    }

    /**
     * Generate thumbnail for image files
     */
    public function generateThumbnail(File $file, $width = 300, $height = 300)
    {
        if (! $this->isImage($file->mime_type)) {
            return false;
        }

        try {
            $originalPath = Storage::disk($file->disk)->path($file->path);
            $thumbnailPath = 'thumbnails/'.pathinfo($file->path, PATHINFO_FILENAME).'_thumb.jpg';

            $image = $this->imageManager->read($originalPath);
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $thumbnailFullPath = Storage::disk($file->disk)->path($thumbnailPath);

            // Ensure directory exists
            $directory = dirname($thumbnailFullPath);
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $image->save($thumbnailFullPath, 80);

            $file->update(['thumbnail_path' => $thumbnailPath]);

            return true;
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate WebP version for image files
     */
    public function generateWebP(File $file)
    {
        if (! $this->isImage($file->mime_type)) {
            return false;
        }

        try {
            $originalPath = Storage::disk($file->disk)->path($file->path);
            $webpPath = 'webp/'.pathinfo($file->path, PATHINFO_FILENAME).'.webp';

            $image = $this->imageManager->read($originalPath);

            $webpFullPath = Storage::disk($file->disk)->path($webpPath);

            // Ensure directory exists
            $directory = dirname($webpFullPath);
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $image->toWebp(80)->save($webpFullPath);

            $file->update([
                'webp_path' => $webpPath,
                'is_optimized' => true,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('WebP generation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Delete a file and its associated files
     */
    public function deleteFile(File $file, $userId = null)
    {
        // Delete main file
        Storage::disk($file->disk)->delete($file->path);

        // Delete thumbnail if exists
        if ($file->thumbnail_path) {
            Storage::disk($file->disk)->delete($file->thumbnail_path);
        }

        // Delete WebP if exists
        if ($file->webp_path) {
            Storage::disk($file->disk)->delete($file->webp_path);
        }

        // Log activity
        if ($userId) {
            $this->logActivity($file->id, $userId, FileActivity::ACTION_DELETE, [
                'file_name' => $file->name,
                'file_size' => $file->size,
            ]);
        }

        // Delete file record
        $file->delete();

        return true;
    }

    /**
     * Get file metadata
     */
    protected function getFileMetadata(UploadedFile $file)
    {
        $metadata = [];

        if ($this->isImage($file->getMimeType())) {
            try {
                $imageSize = getimagesize($file->getPathname());
                if ($imageSize) {
                    $metadata['width'] = $imageSize[0];
                    $metadata['height'] = $imageSize[1];
                    $metadata['aspect_ratio'] = round($imageSize[0] / $imageSize[1], 2);
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $metadata;
    }

    /**
     * Check if file is an image
     */
    protected function isImage($mimeType)
    {
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Log file activity
     */
    protected function logActivity($fileId, $userId, $action, $metadata = [])
    {
        FileActivity::create([
            'file_id' => $fileId,
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats()
    {
        $totalFiles = File::count();
        $totalSize = File::sum('size');
        $publicFiles = File::where('is_public', true)->count();
        $privateFiles = File::where('is_public', false)->count();
        $imageFiles = File::where('mime_type', 'like', 'image/%')->count();
        $videoFiles = File::where('mime_type', 'like', 'video/%')->count();
        $documentFiles = File::whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ])->count();

        // Get total downloads
        $totalDownloads = File::sum('download_count');

        return [
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'total_downloads' => $totalDownloads,
            'public_files' => $publicFiles,
            'private_files' => $privateFiles,
            'image_files' => $imageFiles,
            'video_files' => $videoFiles,
            'document_files' => $documentFiles,
            'other_files' => $totalFiles - $imageFiles - $videoFiles - $documentFiles,
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
