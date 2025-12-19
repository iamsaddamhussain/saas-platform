<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService
    ) {
        //
    }

    public function index()
    {
        $tenants = Tenant::with(['users' => function ($query) {
            $query->where('is_super_admin', false);
        }])
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('SuperAdmin/Tenants/Index', [
            'tenants' => $tenants
        ]);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load([
            'users.role',
            'enabledModules',
        ]);

        $stats = $this->tenantService->getTenantStats($tenant);

        return Inertia::render('SuperAdmin/Tenants/Show', [
            'tenant' => $tenant,
            'stats' => $stats
        ]);
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Tenants/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|regex:/^[a-z0-9-]+$/|unique:tenants,subdomain',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8|confirmed',
            'trial_days' => 'nullable|integer|min:0|max:365',
        ]);

        $tenantData = [
            'name' => $validated['name'],
            'subdomain' => $validated['subdomain'],
            'domain' => $validated['domain'],
            'trial_ends_at' => $validated['trial_days'] 
                ? now()->addDays($validated['trial_days']) 
                : null,
        ];

        $ownerData = [
            'name' => $validated['owner_name'],
            'email' => $validated['owner_email'],
            'password' => $validated['owner_password'],
        ];

        $tenant = $this->tenantService->createTenant([
            ...$tenantData,
            'owner' => $ownerData,
        ]);

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully');
    }

    public function edit(Tenant $tenant)
    {
        return Inertia::render('SuperAdmin/Tenants/Edit', [
            'tenant' => $tenant
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|regex:/^[a-z0-9-]+$/|unique:tenants,subdomain,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'active' => 'boolean',
            'settings.primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'settings.timezone' => 'nullable|string|max:50',
            'settings.locale' => 'nullable|string|max:10',
        ]);

        $settings = $validated['settings'] ?? [];
        unset($validated['settings']);

        $this->tenantService->updateTenant($tenant, [
            ...$validated,
            'settings' => array_merge($tenant->settings ?? [], $settings),
        ]);

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully');
    }

    public function destroy(Tenant $tenant)
    {
        $this->tenantService->deleteTenant($tenant);

        return redirect()
            ->route('super-admin.tenants.index')
            ->with('success', 'Tenant deleted successfully');
    }

    public function impersonate(Tenant $tenant)
    {
        // Find tenant owner or first admin
        $targetUser = $tenant->users()
            ->whereHas('role', function ($query) {
                $query->whereIn('key', ['owner', 'admin']);
            })
            ->first();

        if (!$targetUser) {
            return back()->with('error', 'No admin user found for this tenant');
        }

        // Store original user ID for later restoration
        session(['impersonate_original_user' => Auth::id()]);

        // Login as the target user
        Auth::login($targetUser);

        // Redirect to tenant subdomain
        $url = $tenant->subdomain . '.' . config('app.domain');
        $port = parse_url(config('app.url'), PHP_URL_PORT);
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'http';
        
        $redirectUrl = $scheme . '://' . $url;
        if ($port && $port !== 80 && $port !== 443) {
            $redirectUrl .= ':' . $port;
        }
        
        return redirect($redirectUrl . '/dashboard')
            ->with('success', 'Now impersonating tenant user');
    }

    public function stopImpersonation()
    {
        $originalUserId = session('impersonate_original_user');
        
        if (!$originalUserId) {
            return redirect()->route('login');
        }

        $originalUser = \App\Models\User::find($originalUserId);
        
        if (!$originalUser) {
            return redirect()->route('login');
        }

        Auth::login($originalUser);
        session()->forget('impersonate_original_user');

        // Redirect to super admin dashboard on main domain
        $mainDomain = config('app.domain');
        $port = parse_url(config('app.url'), PHP_URL_PORT);
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http';
        
        $redirectUrl = $scheme . '://' . $mainDomain;
        if ($port && $port !== 80 && $port !== 443) {
            $redirectUrl .= ':' . $port;
        }

        return redirect($redirectUrl . '/super-admin')
            ->with('success', 'Stopped impersonation');
    }
}