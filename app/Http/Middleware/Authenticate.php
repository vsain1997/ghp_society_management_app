<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     return $request->expectsJson() ? null : route('login');
    // }
    protected function redirectTo($request)
    {
        if ($request->is('*/user/v1/*')) {
            // echo 'hiparl';
            // logger('user');
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized '
            ], 401);

        } else {
            // echo 'hi';
            return parent::redirectTo($request);
        }

        // if (!$request->expectsJson()) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        // return route('/');
    }

}
