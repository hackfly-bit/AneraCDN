<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Relationships
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function fileActivities(): HasMany
    {
        return $this->hasMany(FileActivity::class);
    }

    // Tambahkan relasi yang hilang untuk ApiKey
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    // Methods
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('admin');
    }

    public function canUploadFiles(): bool
    {
        return $this->hasPermissionTo('upload files') || $this->isAdmin();
    }

    public function canDeleteFiles(): bool
    {
        return $this->hasPermissionTo('delete files') || $this->isAdmin();
    }

    public function canManageAllFiles(): bool
    {
        return $this->hasPermissionTo('manage all files') || $this->isAdmin();
    }
}
