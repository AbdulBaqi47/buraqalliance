<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use Carbon\Carbon;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:web');
    }
    /**
     * Route: central.admin.tenants.data
     * PageRoute: central.admin.tenants.view
    */
    public function getTenants(Request $request)
    {
        $tenants = Tenant::with([
            'domain'
        ])
        ->get();


        return Datatables::of($tenants)
        ->addColumn('actions', function($tenant){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('domain_name', function($tenant){
            return isset($tenant->domain) ? $tenant->domain->domain : "No Domain";
        })
        ->rawColumns(['actions'])
        ->removeColumn('domain')
        ->make(true);
    }
}
