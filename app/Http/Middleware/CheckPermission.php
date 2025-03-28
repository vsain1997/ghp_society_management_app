<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next, $permission)
    // {
    //     $user = Auth::user();

    //     // Check if the user has the permission from model_has_permissions table only
    //     if (!$user || !$user->hasPermissionFromModelOnly($permission)) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, $permissions)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Split the permissions by the `|` delimiter to support multiple permissions
        $permissionsArray = explode('|', $permissions);

        // Check if the user has any of the specified permissions in the model_has_permissions table
        $hasPermission = \DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('permission_id', function ($query) use ($permissionsArray) {
                $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', $permissionsArray);
            })
            ->exists();

        if (!$hasPermission) {
            // abort(403, 'Unauthorized action.');
            //redirect to route dashboard
            return redirect()->route('admin.dashboard')->with([
                'status' => 'error',
                'message' => 'Unauthorized Access!'
            ]);

        }

        return $next($request);
    }


}
