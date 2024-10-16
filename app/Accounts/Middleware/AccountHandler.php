<?php

namespace App\Accounts\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccountHandler
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
        dd($request->all());
        return $next($request);
    }
}
