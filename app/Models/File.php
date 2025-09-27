<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'slug',
        'path',
        'disk',
        'mime_type',
        'extension',
        'size',
        'hash',
        'metadata',
        'is_public',
        'is_optimized',
        'thumbnail_path',
        'webp_path',
        'user_id',
        'folder',
        'download_count',
        'last_accessed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_optimized' => 'boolean',
        'last_accessed_at' => 'datetime',
        'size' => 'integer',
        'download_count' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($file) {
            if (empty($file->slug)) {
                $file->slug = Str::uuid();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(FileActivity::class);
    }

    // Accessors & Mutators
    public function getUrlAttribute(): string
    {
        if ($this->is_public) {
            return Storage::disk($this->disk)->url($this->path);
        }
        
        return route('files.download', $this->slug);
    }

    public function getCdnUrlAttribute(): string
    {
        $cdnUrl = config('app.cdn_url', config('app.url'));
        return $cdnUrl . '/storage/' . $this->path;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        
        return Storage::disk($this->disk)->url($this->thumbnail_path);
    }

    public function getWebpUrlAttribute(): ?string
    {
        if (!$this->webp_path) {
            return null;
        }
        
        return Storage::disk($this->disk)->url($this->webp_path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByMimeType($query, $mimeType)
    {
        return $query->where('mime_type', 'like', $mimeType . '%');
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ]);
    }

    // Methods
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_accessed_at' => now()]);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ]);
    }
}
