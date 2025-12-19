<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'icon',
        'active',
        'core',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'core' => 'boolean',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot(['enabled', 'settings'])
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'module_permissions')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeCore($query)
    {
        return $query->where('core', true);
    }

    public function scopeNonCore($query)
    {
        return $query->where('core', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key, $default);
    }

    public function updateConfig(array $newConfig): void
    {
        $this->update([
            'config' => array_merge($this->config ?? [], $newConfig)
        ]);
    }
}