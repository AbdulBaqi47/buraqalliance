<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Services\InjectService;

class CheckRole
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
        // return $next($request);

        # Payload
        $route = $request->route();
        $route_name = $route->getName();
        $route_path = $route->uri();
        $request_method=$request->method();
        $is_post = false;
        if(strtolower($request_method)=='post')$is_post=true;

        # We need to find the route with same path and use its name
        $all_routes = app('helper_service')->routes->getRegisteredRoutes();

        if(!isset($route_name) || !!preg_match('/^generated::/', $route_name)){
            // Route name is not found
            // Most of the cases it is POST route having GET route of same "path"
            $gRoute = $all_routes
            ->first(function($item) use ($route_path){
                return $item->path === $route_path && strtolower($item->method) === 'get';
            });
            if(isset($gRoute)){
                $route_name = $gRoute->name;
            }
        }


        if(!isset($route_name)){
            # Still not found? it is not worth it, restrict the route
            return $this->deny_access($request);
        }

        # Pass the route name to fucntion, it will check for access

        if(!app('helper_service')->routes->has_access($route_name, $is_post)){
            return $this->deny_access($request, $route_path . ' - ' . $route_name);
        }
        return $next($request);
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
