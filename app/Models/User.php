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
        'avatar', // Tambah in
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

    // Accessor untuk avatar URL
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::exists('public/avatars/' . $this->avatar)) {
        return Storage::url('avatars/' . $this->avatar);
        }
    
        return asset('sneat/assets/img/avatars/1.png'); // Default avatar
    }
}