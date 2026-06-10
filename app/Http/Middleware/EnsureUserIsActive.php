<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next, string $guard = 'staff'): Response
    {
        $user = $request->user() ?? auth($guard)->user();

        if ($user && ! $user->is_active) {
            auth($guard)->logout();
            $route = $guard === 'customer' ? 'customer.login' : 'staff.login';

            return redirect()->route($route)->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
