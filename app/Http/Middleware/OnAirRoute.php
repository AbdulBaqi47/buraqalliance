<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Services\InjectService;

class OnAirRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $modal_width='30%')
    {

        # This service will help us
        $helper_service = new InjectService();

        # Need to check if current reqeust is http or ajax
        # If http: we will load a global view that will have the same request as KR AJAX module, so it can be loaded in modal
        # If ajax: we will next() is.

        $request_type = $helper_service->helper->request_type();

        // echo '<span>'.$request_type.'</span>';

        if($request_type=='http') {
            return response(view('Tenant.global.kingview', compact('modal_width')));
        }

        # Return normal content
        return $next($request);
    }

}
