<?php

namespace App\Services\Injected;

use App\Models\Tenant\Investor;
use App\Models\Tenant\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
| ----------------------------------------------------
|   Responsible for calling some functions in views.
| ----------------------------------------------------
|
|
|
|
*/
class RouteService
{
    /**
     * Create new instance of RouteService.
     *
     */
    public function __construct()
    {
    }

    private $routeCollection=null;

    public function has_access($route_name, $is_post=false)
    {
        # Check if logged in user is admin
        $user = Auth::user();
        // If No User Found Then Return false
        if(!$user){
            return false;
        }

        # Admin will have access to all routes
        if($user->type=='su')return true;

        # Eager load granted routes
        $user->loadMissing('granted_routes');

        # Fetch all the granted routes for this user
        $granted_route = $user->granted_routes->where('route_name', $route_name)->first();

        if(isset($granted_route)){
            # Seems user have access to this route, Check for soft access
            if($granted_route->has_soft===true){
                # it means we should deny the post request
                if($is_post)return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Will check custom access against employee
     *
     * @param string $tag module name like entry_access
     * @param array $access_data array of partial access, if not provide or is null, it will check for all access
    */
    public function has_custom_access($tag, $access_data = null)
    {
        $user = Auth::user();

        # Deny Access
        if(!$user) return false;

        # ADMINS will have access to all data
        if($user->is_admin) return true;

        # Find role against tag, if not found, deny access
        $employee_role = $user->getCustomRole($tag);
        if(!isset($employee_role)) return false;

        # Check if access to all module
        if($employee_role->access_scope === "all") return true;

        if(isset($access_data) && isset($employee_role->access_data)){
            # Check for partial access
            # : Every item of $access_data should be present in employee access in order to grant access
            $found = collect($access_data)->every(function($item) use ($employee_role){
                return in_array($item, $employee_role->access_data);
            });

            return $found;
        }

        return false;
    }

    public function getRegisteredRoutes()
    {
        # Get all available routes
        $routeCollection = isset($this->routeCollection)?$this->routeCollection:Route::getRoutes();

        $routes=collect([]);
        foreach ($routeCollection as $route) {
            #Set required data to variables
            $url=$route->uri();
            $middlewares = $route->gatherMiddleware();

            $excluded_middlewares = $route->excludedMiddleware();
            // removes the items from 1st array, presented on 2nd array
            // ref: https://www.php.net/manual/en/function.array-diff.php
            $middlewares = array_diff( $middlewares, $excluded_middlewares );

            # fetch routes only eligible
            if(in_array("check_role", $middlewares)){
                $routes->push((object)[
                    'method'=>$route->methods()[0],
                    'path'=>$url,
                    'middleware'=>$middlewares,
                    'name'=>$route->getName(),
                    'action'=>$route->getActionName(),
                    'full_path'=>url($url),


                    'prefix'=>$route->getPrefix(),
                    'compiled'=>$route->getCompiled(),
                ]);
            }
        }

        # Mark GET request that has POST requests (for soft_access purposes)
        foreach ($routes as $key=>$route) {
            $has_post = false;
            # Check if route is GET
            if(strtolower($route->method)=='get'){
                # Now find the route with same path

                $route_found = $routes->where('path', $route->path)->where('method', 'POST')->first();
                if(isset($route_found))$has_post = true;
            }

            #remove post requests with null name
            if($route->name==null || !!preg_match('/^generated::/', $route->name))$routes->forget($key);
            $route->has_post=$has_post;
        }

        return collect(array_values($routes->toArray()));
    }
}
