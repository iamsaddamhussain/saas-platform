<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantService
{
    public function createTenant(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $data['name'],
                'subdomain' => $this->generateSubdomain($data['subdomain'] ?? $data['name']),
                'domain' => $data['domain'] ?? null,
                'settings' => $data['settings'] ?? [],
                'primary_color' => $data['primary_color'] ?? '#3B82F6',
                'timezone' => $data['timezone'] ?? 'UTC',
                'locale' => $data['locale'] ?? 'en',
                'trial_ends_at' => $data['trial_ends_at'] ?? now()->addDays(14),
            ]);

            // Enable core modules for tenant
            $this->enableCoreModules($tenant);

            // Create tenant owner if provided
            if (isset($data['owner'])) {
                $this->createTenantOwner($tenant, $data['owner']);
            }

            // Seed initial data
            $this->seedTenantData($tenant);

            return $tenant;
        });
    }

    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant;
    }

    public function deleteTenant(Tenant $tenant): bool
    {
        return DB::transaction(function () use ($tenant) {
            // Delete all tenant users
            $tenant->users()->delete();

            // Detach all modules
            $tenant->modules()->detach();

            // Delete tenant
            return $tenant->delete();
        });
    }

    public function createTenantOwner(Tenant $tenant, array $ownerData): User
    {
        // Get or create owner role
        $ownerRole = Role::where('key', 'owner')->first();
        if (!$ownerRole) {
            $ownerRole = Role::create([
                'name' => 'Owner',
                'key' => 'owner',
                'description' => 'Tenant owner with full access',
                'is_system' => true,
            ]);
        }

        return User::create([
            'name' => $ownerData['name'],
            'email' => $ownerData['email'],
            'password' => Hash::make($ownerData['password']),
            'tenant_id' => $tenant->id,
            'role_id' => $ownerRole->id,
            'email_verified_at' => now(),
            'active' => true,
        ]);
    }

    public function enableModuleForTenant(Tenant $tenant, string $moduleKey, array $settings = []): void
    {
        $module = Module::where('key', $moduleKey)->first();
        if (!$module) {
            throw new \Exception("Module '{$moduleKey}' not found");
        }

        $tenant->modules()->syncWithoutDetaching([
            $module->id => [
                'enabled' => true,
                'settings' => json_encode($settings),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function disableModuleForTenant(Tenant $tenant, string $moduleKey): void
    {
        $module = Module::where('key', $moduleKey)->first();
        if (!$module) {
            return;
        }

        // Don't disable core modules
        if ($module->core) {
            throw new \Exception("Cannot disable core module '{$moduleKey}'");
        }

        $tenant->modules()->updateExistingPivot($module->id, [
            'enabled' => false,
            'updated_at' => now(),
        ]);
    }

    private function generateSubdomain(string $name): string
    {
        $subdomain = Str::slug(strtolower($name));
        
        // Ensure uniqueness
        $original = $subdomain;
        $counter = 1;
        
        while (Tenant::where('subdomain', $subdomain)->exists()) {
            $subdomain = $original . '-' . $counter;
            $counter++;
        }

        return $subdomain;
    }

    private function enableCoreModules(Tenant $tenant): void
    {
        $coreModules = Module::where('core', true)->get();
        
        foreach ($coreModules as $module) {
            $tenant->modules()->attach($module->id, [
                'enabled' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedTenantData(Tenant $tenant): void
    {
        // Override this method to seed tenant-specific data
        // For example: create default categories, settings, etc.
    }

    public function getTenantStats(Tenant $tenant): array
    {
        return [
            'users_count' => $tenant->users()->count(),
            'active_users_count' => $tenant->users()->active()->count(),
            'modules_count' => $tenant->enabledModules()->count(),
            'storage_used' => 0, // Implement storage calculation
            'created_at' => $tenant->created_at,
            'trial_ends_at' => $tenant->trial_ends_at,
            'is_on_trial' => $tenant->isOnTrial(),
        ];
    }
}