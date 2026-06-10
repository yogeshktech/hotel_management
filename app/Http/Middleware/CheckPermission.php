<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $staff = auth('staff')->user();

        if (! $staff) {
            abort(403, 'Unauthorized');
        }

        if ($staff->hasRole('super_admin')) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($staff->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}
