<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckResourcePermission
{
    /**
     * Map resource controller methods to permission keys.
     */
    protected array $map = [
        'index' => '.view',
        'show' => '.view',
        'create' => '.add',
        'store' => '.add',
        'edit' => '.edit',
        'update' => '.edit',
        'destroy' => '.delete',
    ];

    public function handle(Request $request, Closure $next, string $prefix): Response
    {
        $action = $request->route()?->getActionName() ?? '';

        if ($action && str_contains($action, '@')) {
            $method = class_basename(explode('@', $action, 2)[1]);
            $suffix = $this->map[$method] ?? null;

            if ($suffix && ! $request->user()?->canDo($prefix.$suffix)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
