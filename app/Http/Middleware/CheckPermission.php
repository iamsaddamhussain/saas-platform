<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Permission denied',
                    'message' => "You don't have permission to access this resource."
                ], 403);
            }

            abort(403, "You don't have permission to access this resource.");
        }

        return $next($request);
    }
}