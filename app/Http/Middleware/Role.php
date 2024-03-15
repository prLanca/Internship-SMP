<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('worker')) {
            // Redirect users without the required roles to the appropriate route
            return redirect()->route('index');
        }

        return $next($request);

    }

}
