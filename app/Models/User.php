<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'inspection_center_id',
        'role',
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

    public function inspectionCenter(): BelongsTo
    {
        return $this->belongsTo(InspectionCenter::class);
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCenterManager(): bool
    {
        return $this->role === 'center_manager';
    }

    public function isInspector(): bool
    {
        return $this->role === 'inspector';
    }

    public function canManageSystem(): bool
    {
        return $this->isAdmin();
    }

    public function canWorkWithVehicles(): bool
    {
        return in_array($this->role, [
            'admin',
            'center_manager',
            'inspector',
        ], true);
    }
}
