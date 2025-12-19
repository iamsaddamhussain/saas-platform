<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function hasPermission(string $permissionKey): bool
    {
        return $this->permissions()
            ->where('permissions.key', $permissionKey)
            ->exists();
    }

    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('key', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);
    }

    public function revokePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('key', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);
    }

    public function syncPermissions(array $permissions): void
    {
        $permissionIds = Permission::whereIn('key', $permissions)->pluck('id');
        $this->permissions()->sync($permissionIds);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeNonSystem($query)
    {
        return $query->where('is_system', false);
    }
}