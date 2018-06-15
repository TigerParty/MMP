<?php

namespace App\Http\Middleware;

use Closure;

class AdminAccessible
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!argo_is_accessible(config('argodf.admin_function_priority'))) {
            abort(401);
        }

        return $next($request);
    }
}
