<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'domain',
        'settings',
        'logo_path',
        'primary_color',
        'timezone',
        'locale',
        'active',
        'trial_ends_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['enabled', 'settings'])
            ->withTimestamps();
    }

    public function enabledModules(): BelongsToMany
    {
        return $this->modules()->wherePivot('enabled', true);
    }

    public function hasModule(string $moduleKey): bool
    {
        return $this->enabledModules()
            ->where('modules.key', $moduleKey)
            ->exists();
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

    public function scopeBySubdomain($query, string $subdomain)
    {
        return $query->where('subdomain', $subdomain);
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialEnded(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }
}