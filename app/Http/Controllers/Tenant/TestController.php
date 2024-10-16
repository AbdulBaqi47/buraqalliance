<?php

namespace App\Http\Controllers\Tenant;

use App\Helpers\GoogleSheets;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Bike;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Ledger;
use Illuminate\Http\Request;

use App\Models\Tenant\Supplier;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleLedgerItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Revolution\Google\Sheets\Facades\Sheets;

class TestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }

    public function index()
    {
        return 'Disabled by admin';


        # ------------------------
        # Data migration from KR
        # ------------------------


    }

}
