<?php

namespace App\Helpers;

use App\Models\File;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class SignedUrlHelper
{
    /**
     * Generate a signed URL for file download
     */
    public static function generateDownloadUrl(File $file, int $expirationMinutes = 60): string
    {
        $expiration = Carbon::now()->addMinutes($expirationMinutes);
        
        return URL::temporarySignedRoute(
            'files.signed-download',
            $expiration,
            ['slug' => $file->slug]
        );
    }
    
    /**
     * Generate a signed URL for file viewing
     */
    public static function generateViewUrl(File $file, int $expirationMinutes = 60): string
    {
        $expiration = Carbon::now()->addMinutes($expirationMinutes);
        
        return URL::temporarySignedRoute(
            'files.signed-view',
            $expiration,
            ['slug' => $file->slug]
        );
    }
    
    /**
     * Generate a signed URL for API access
     */
    public static function generateApiUrl(File $file, int $expirationMinutes = 60): string
    {
        $expiration = Carbon::now()->addMinutes($expirationMinutes);
        
        return URL::temporarySignedRoute(
            'api.files.signed-download',
            $expiration,
            ['slug' => $file->slug]
        );
    }
    
    /**
     * Generate a signed URL for thumbnail
     */
    public static function generateThumbnailUrl(File $file, int $expirationMinutes = 60): ?string
    {
        if (!$file->thumbnail_path) {
            return null;
        }
        
        $expiration = Carbon::now()->addMinutes($expirationMinutes);
        
        return URL::temporarySignedRoute(
            'files.signed-thumbnail',
            $expiration,
            ['slug' => $file->slug]
        );
    }
    
    /**
     * Generate a signed URL for WebP version
     */
    public static function generateWebpUrl(File $file, int $expirationMinutes = 60): ?string
    {
        if (!$file->webp_path) {
            return null;
        }
        
        $expiration = Carbon::now()->addMinutes($expirationMinutes);
        
        return URL::temporarySignedRoute(
            'files.signed-webp',
            $expiration,
            ['slug' => $file->slug]
        );
    }
    
    /**
     * Generate multiple signed URLs for a file
     */
    public static function generateAllUrls(File $file, int $expirationMinutes = 60): array
    {
        return [
            'download' => self::generateDownloadUrl($file, $expirationMinutes),
            'view' => self::generateViewUrl($file, $expirationMinutes),
            'api' => self::generateApiUrl($file, $expirationMinutes),
            'thumbnail' => self::generateThumbnailUrl($file, $expirationMinutes),
            'webp' => self::generateWebpUrl($file, $expirationMinutes),
        ];
    }
    
    /**
     * Check if a signed URL is valid
     */
    public static function isValidSignedUrl(string $url): bool
    {
        try {
            $request = request();
            $request->setUrl($url);
            
            return $request->hasValidSignature();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get expiration time from signed URL
     */
    public static function getExpirationTime(string $url): ?Carbon
    {
        try {
            $parsed = parse_url($url);
            parse_str($parsed['query'] ?? '', $query);
            
            if (isset($query['expires'])) {
                return Carbon::createFromTimestamp($query['expires']);
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
