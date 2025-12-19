<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if (!$tenant) {
            return $this->handleTenantNotFound($request);
        }

        if (!$tenant->active) {
            return $this->handleInactiveTenant($request);
        }

        // Set tenant context
        app()->instance('tenant', $tenant);
        config(['app.current_tenant' => $tenant]);

        return $next($request);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        $host = $request->getHost();
        
        // Extract subdomain
        $subdomain = $this->extractSubdomain($host);
        
        if (!$subdomain) {
            return null;
        }

        // Cache tenant lookup for performance
        return Cache::remember(
            "tenant.{$subdomain}",
            now()->addMinutes(60),
            fn() => Tenant::where('subdomain', $subdomain)->active()->first()
        );
    }

    private function extractSubdomain(string $host): ?string
    {
        // Remove www. if present
        $host = preg_replace('/^www\./', '', $host);
        
        // Get base domain from config
        $baseDomain = config('app.domain', 'saas-platform.localhost');
        
        // Check if this is a subdomain
        if (str_ends_with($host, ".{$baseDomain}")) {
            $subdomain = str_replace(".{$baseDomain}", '', $host);
            return $subdomain !== '' ? $subdomain : null;
        }

        // Check for custom domain
        $tenant = Cache::remember(
            "tenant.domain.{$host}",
            now()->addMinutes(60),
            fn() => Tenant::where('domain', $host)->active()->first()
        );

        return $tenant?->subdomain;
    }

    private function handleTenantNotFound(Request $request): Response
    {
        // If this is an API request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'The requested tenant could not be found.'
            ], 404);
        }

        // Redirect to main app or show error page
        return redirect('https://' . config('app.domain') . '/tenant-not-found');
    }

    private function handleInactiveTenant(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant inactive',
                'message' => 'This tenant account is currently inactive.'
            ], 403);
        }

        return redirect('https://' . config('app.domain') . '/tenant-inactive');
    }
}