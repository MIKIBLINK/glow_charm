<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Ensure the authenticated user has the given permission.
     * Admins always pass (User::canDo returns true for admins).
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->canDo($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
