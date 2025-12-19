<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::active()->count(),
            'total_users' => User::where('is_super_admin', false)->count(),
            'active_users' => User::where('is_super_admin', false)->active()->count(),
            'tenants_on_trial' => Tenant::whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now())
                ->count(),
            'trial_ending_soon' => Tenant::whereNotNull('trial_ends_at')
                ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                ->count(),
        ];

        $recentTenants = Tenant::with('users')
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::with(['tenant', 'role'])
            ->where('is_super_admin', false)
            ->latest()
            ->limit(10)
            ->get();

        $moduleUsage = Module::withCount([
                'tenants as enabled_count' => function ($query) {
                    $query->where('tenant_modules.enabled', true);
                }
            ])
            ->get()
            ->map(function ($module) {
                $totalTenants = Tenant::count();
                return [
                    'name' => $module->name,
                    'key' => $module->key,
                    'enabled_count' => $module->enabled_count,
                    'usage_percentage' => $totalTenants > 0 ? round(($module->enabled_count / $totalTenants) * 100, 1) : 0,
                ];
            });

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'recentTenants' => $recentTenants,
            'recentUsers' => $recentUsers,
            'moduleUsage' => $moduleUsage,
        ]);
    }
}