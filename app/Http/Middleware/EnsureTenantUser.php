<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = app('tenant');

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admins can access any tenant
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Check if user belongs to this tenant
        if (!$user->belongsToTenant($tenant->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied',
                    'message' => 'You do not have access to this tenant.'
                ], 403);
            }

            return redirect()->route('login')->with('error', 'You do not have access to this tenant.');
        }

        // Check if user is active
        if (!$user->active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Account inactive',
                    'message' => 'Your account is currently inactive.'
                ], 403);
            }

            auth()->guard()->logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with('error', 'Your account is currently inactive.');
        }

        return $next($request);
    }
}