<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkSecurityGuardApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow both 'application/json' and 'application/x-www-form-urlencoded'
        $contentType = $request->header('Content-Type');

        // header must be set to 'application/json' or 'application/x-www-form-urlencoded'
        if (!$request->expectsJson() && strpos($contentType, 'application/x-www-form-urlencoded') === false) {
            return res(
                status: false,
                message: "Accept header must be application/json or application/x-www-form-urlencoded",
                code: HTTP_NOT_ACCEPTABLE
            );
        }

        // If content is provided, validate the body
        if (!empty($request->getContent())) {
            // Check if it's JSON when the content type is set to application/json
            if (strpos($contentType, 'application/json') !== false && !$request->isJson()) {
                return res(
                    status: false,
                    message: "Request Body must be in JSON format",
                    code: HTTP_NOT_ACCEPTABLE
                );
            }
        }

        // Check if token is missing
        if (!$request->bearerToken()) {
            return res(
                status: false,
                message: "Token is missing!",
                code: HTTP_UNAUTHORIZED
            );
        }

        // Check if token is valid and authenticated & user is a staff_security_guard
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();

            if ($user->role == 'staff_security_guard') {
                // Proceed to the next middleware if the checks pass
                return $next($request);
            }

            // Return error for other roles
            return res(
                status: false,
                message: "Unauthorized Access!",
                code: HTTP_UNAUTHORIZED
            );
        }

        // Return an error if the token is invalid or expired
        return res(
            status: false,
            message: "Unauthorized Access, Token invalid or expired!",
            code: HTTP_UNAUTHORIZED
        );
    }
}
