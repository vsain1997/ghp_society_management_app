<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                $user = Auth::user();
                // Redirect based on user role
                if ($user->role === 'super_admin') {
                    return redirect()->route('superadmin.dashboard')->with('successMessage', 'You are already logged in !');
                }
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard')->with('successMessage', 'You are already logged in !');
                }

                //default redirect
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
