<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we're impersonating
        if (session('impersonate_original_user') && $request->user()) {
            // Share impersonation status with all views
            view()->share('isImpersonating', true);
            view()->share('originalUserId', session('impersonate_original_user'));
        } else {
            view()->share('isImpersonating', false);
        }

        return $next($request);
    }
}