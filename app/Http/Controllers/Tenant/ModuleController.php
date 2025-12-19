<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        
        $modules = Module::active()
            ->ordered()
            ->withCount([
                'tenants as enabled_count' => function ($query) {
                    $query->where('tenant_modules.enabled', true);
                }
            ])
            ->get()
            ->map(function ($module) use ($tenant) {
                $tenantModule = $tenant->modules()
                    ->where('modules.id', $module->id)
                    ->first();
                
                $module->is_enabled = $tenantModule ? $tenantModule->pivot->enabled : false;
                $module->settings = $tenantModule ? $tenantModule->pivot->settings : [];
                
                return $module;
            });

        return Inertia::render('Tenant/Modules/Index', [
            'modules' => $modules,
            'tenant' => $tenant,
        ]);
    }

    public function toggle(Request $request, Module $module)
    {
        $tenant = app('tenant');
        
        // Check if user has permission
        if (!$request->user()->hasPermission('modules.manage')) {
            return back()->with('error', 'You do not have permission to manage modules.');
        }

        // Prevent disabling core modules
        if ($module->core) {
            return back()->with('error', 'Core modules cannot be disabled.');
        }

        $tenantModule = $tenant->modules()
            ->where('modules.id', $module->id)
            ->first();

        if ($tenantModule) {
            // Toggle existing module
            $newStatus = !$tenantModule->pivot->enabled;
            $tenant->modules()->updateExistingPivot($module->id, [
                'enabled' => $newStatus,
                'updated_at' => now(),
            ]);
            
            $message = $newStatus 
                ? "Module '{$module->name}' has been enabled." 
                : "Module '{$module->name}' has been disabled.";
        } else {
            // Enable new module
            $tenant->modules()->attach($module->id, [
                'enabled' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $message = "Module '{$module->name}' has been enabled.";
        }

        return back()->with('success', $message);
    }
}