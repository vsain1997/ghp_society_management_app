<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Society;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && auth()->user()->role == "admin") {
            $user = auth()->user()->load('member');
            if ($user->member && $user->member->status != "active") {
                Auth::logout(); // Log the user out
                return redirect()->route('admin.login.form')->with([
                    'status' => 'error',
                    'message' => 'Your account is inactive. Please contact SuperAdmin.',
                ]);
            } else {
                // Check if the user is associated with a member and the member's status
                if ($user->member) {
                    $society_id = $user->member->society_id;

                    // Check the society status
                    $society = Society::find($society_id);
                    if ($society && $society->status == "inactive") {
                        // Redirect or deny access
                        Auth::logout(); // Log the user out
                        return redirect()->route('admin.login.form')->with([
                            'status' => 'error',
                            'message' => 'Society is inactive. Please contact SuperAdmin.',
                        ]);
                    }
                }

                return $next($request);
            }
        } else {
            // echo 'hello admin.login.form';
            // exit;
            Auth::logout();

            return redirect()->route('admin.login.form');
        }
    }
}
