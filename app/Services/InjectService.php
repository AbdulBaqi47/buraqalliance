<?php

namespace App\Services;
use Auth;

use App\Services\Injected\RouteService;
use App\Services\Injected\HelperService;

/*
| ----------------------------------------------------
|   Responsible for calling some functions in views.
| ----------------------------------------------------
|
|
|
|
*/
class InjectService
{

    /**
     * The RouteService instance.
     * 
     * @var RouteService
     */
    public $routes;

    public $helper;



    /**
     * Create new instance of InjectService.
     * 
     * @param RouteService $routes
     */
    public function __construct()
    {
        $this->routes = new RouteService();
        $this->helper = new HelperService();
    }

    
    

}