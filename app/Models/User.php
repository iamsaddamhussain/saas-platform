<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role_id',
        'is_super_admin',
        'settings',
        'last_login_at',
        'active',
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
            'is_super_admin' => 'boolean',
            'settings' => 'array',
            'last_login_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permissionKey): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->role?->hasPermission($permissionKey) ?? false;
    }

    public function can($ability, $arguments = []): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        // Check custom permissions first
        if ($this->hasPermission($ability)) {
            return true;
        }

        // Fall back to Laravel's default authorization
        return parent::can($ability, $arguments);
    }

    public function hasRole(string $roleKey): bool
    {
        return $this->role?->key === $roleKey;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function isTenantAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('owner');
    }

    public function belongsToTenant(int $tenantId): bool
    {
        return $this->tenant_id === $tenantId;
    }

    public function getSettings(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->settings;
        }

        return data_get($this->settings, $key, $default);
    }

    public function updateSettings(array $newSettings): void
    {
        $this->update([
            'settings' => array_merge($this->settings ?? [], $newSettings)
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super_admin', true);
    }
}
