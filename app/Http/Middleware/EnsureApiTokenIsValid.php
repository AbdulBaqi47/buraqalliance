<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class EnsureApiTokenIsValid
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
        if ($request->input('token') !== config('kingriders.apikey')) {
            if($request->ajax()){
                return response()->json([
                    'message' => 'Invalid token'
                ], 404);
            }
            else abort(404);
        }

        return $next($request);
    }
}
