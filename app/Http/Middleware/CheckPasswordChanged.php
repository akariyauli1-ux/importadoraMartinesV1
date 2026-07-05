<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->password_changed) {
            // Prevent infinite redirection loop: allow access to the password change route and logout
            if (!$request->routeIs('change-password') && !$request->routeIs('logout')) {
                return redirect()->route('change-password')
                    ->with('warning', 'Debe cambiar su contraseña por defecto antes de continuar utilizando el sistema.');
            }
        }

        return $next($request);
    }
}
