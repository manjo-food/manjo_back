<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $rolesArray = explode('|', $roles);

        foreach ($rolesArray as $role) {
            if (auth()->user()->hasRole($role)) {
                return $next($request);
            }
        }

        throw new UnauthorizedException();
    }
}
