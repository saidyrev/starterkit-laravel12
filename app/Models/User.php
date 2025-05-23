<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
        return $this->role && $this->role->name === $role;
    }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->permissions->contains('name', $permission);
    }

    public function hasAnyPermission($permissions)
    {
        if (!$this->role) return false;
        
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * FIXED: Accessor untuk avatar URL dengan handling berbagai format path
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Format 1: Jika avatar menyimpan full path (avatars/filename.jpg)
            if (Storage::disk('public')->exists($this->avatar)) {
                return Storage::disk('public')->url($this->avatar);
            }
            
            // Format 2: Jika avatar hanya menyimpan filename (filename.jpg)
            if (Storage::disk('public')->exists('avatars/' . $this->avatar)) {
                return Storage::disk('public')->url('avatars/' . $this->avatar);
            }
            
            // Format 3: Legacy check untuk public/avatars/
            if (Storage::exists('public/avatars/' . $this->avatar)) {
                return Storage::url('avatars/' . $this->avatar);
            }
        }
    
        // Default avatar jika tidak ada atau file tidak ditemukan
        return asset('sneat/assets/img/avatars/1.png');
    }

    /**
     * Get avatar path for storage operations
     */
    public function getAvatarPath()
    {
        if (!$this->avatar) {
            return null;
        }

        // Check berbagai kemungkinan path
        $possiblePaths = [
            $this->avatar, // Full path format
            'avatars/' . $this->avatar, // Filename only format
        ];

        foreach ($possiblePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        }

        // Legacy check
        if (Storage::exists('public/avatars/' . $this->avatar)) {
            return 'avatars/' . $this->avatar;
        }

        return null;
    }

    /**
     * Get Gravatar URL sebagai alternatif
     */
    public function getGravatarUrl($size = 400)
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s={$size}";
    }

    /**
     * Check if user has custom avatar
     */
    public function hasCustomAvatar()
    {
        return $this->avatar && $this->getAvatarPath() !== null;
    }

    /**
     * Model events untuk auto cleanup avatar
     */
    protected static function booted()
    {
        // Cleanup avatar saat user dihapus
        static::deleting(function ($user) {
            $user->deleteAvatar();
        });

        // Cleanup avatar lama saat avatar diupdate
        static::updating(function ($user) {
            if ($user->isDirty('avatar') && $user->getOriginal('avatar')) {
                $user->deleteOldAvatar($user->getOriginal('avatar'));
            }
        });
    }

    /**
     * Delete current user avatar
     */
    public function deleteAvatar()
    {
        if ($this->avatar) {
            $this->deleteOldAvatar($this->avatar);
        }
    }

    /**
     * Delete specific avatar file dengan handling berbagai format path
     */
    private function deleteOldAvatar($avatarPath)
    {
        if (!$avatarPath) return;

        $pathsToCheck = [
            $avatarPath, // Full path
            'avatars/' . $avatarPath, // Filename only
            'avatars/' . basename($avatarPath), // Extract filename jika full path
        ];

        foreach ($pathsToCheck as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                break;
            }
        }

        // Legacy cleanup
        if (Storage::exists('public/avatars/' . basename($avatarPath))) {
            Storage::delete('public/avatars/' . basename($avatarPath));
        }
    }

    /**
     * Update avatar dan cleanup otomatis
     */
    public function updateAvatar($newAvatarPath)
    {
        $oldAvatar = $this->avatar;
        
        $this->update(['avatar' => $newAvatarPath]);
        
        // Manual cleanup jika event tidak trigger
        if ($oldAvatar && $oldAvatar !== $newAvatarPath) {
            $this->deleteOldAvatar($oldAvatar);
        }
    }
}