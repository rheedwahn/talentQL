<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class PhotographerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->role->name === Role::PHOTOGRAPHER) {
            return $next($request);
        }
        return response()->json(['status' => 'error', 'message' => 'User does not have the right access'], 403);
    }
}
