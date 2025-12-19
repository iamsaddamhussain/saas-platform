<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        $user = Auth::user();

        $enabledModules = $tenant->enabledModules()
            ->ordered()
            ->get();

        $stats = [
            'tenant_name' => $tenant->name,
            'total_users' => $tenant->users()->count(),
            'active_users' => $tenant->users()->active()->count(),
            'enabled_modules' => $enabledModules->count(),
            'is_on_trial' => $tenant->isOnTrial(),
            'trial_ends_at' => $tenant->trial_ends_at,
        ];

        // Get recent activity (you can expand this based on your needs)
        $recentUsers = $tenant->users()
            ->with('role')
            ->latest('last_login_at')
            ->limit(5)
            ->get();

        return Inertia::render('Tenant/Dashboard', [
            'tenant' => $tenant,
            'user' => $user,
            'stats' => $stats,
            'enabledModules' => $enabledModules,
            'recentUsers' => $recentUsers,
        ]);
    }
}