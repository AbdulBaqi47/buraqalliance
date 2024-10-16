<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CopyHeaders
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
        $response = $next($request);

        $hasNofetchHeader = $request->hasHeader('X-NOFETCH');

        if($hasNofetchHeader){
            $response->headers->set('X-NOFETCH', 'Y');
        }

        return $response;
    }

    /**
     * Deny access and return response
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
    */
    private function deny_access(Request $request, $route_name=null)
    {
        # For ajax request, we might need to send ajax response
        if($request->ajax()){

            if($route_name){
                return response($route_name, 403);
            }
            return response(null, 403);
        }
        # Otherwise, just return the 403 page
        return response(view("403"));
    }
}
