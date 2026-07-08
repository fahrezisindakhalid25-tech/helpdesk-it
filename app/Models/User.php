<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'permissions',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if (in_array('*', $this->permissions ?? [])) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function isAdmin(): bool
    {
        return in_array('*', $this->permissions ?? []);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
