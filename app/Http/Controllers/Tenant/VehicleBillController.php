<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Tenant\Bike;
use App\Models\Tenant\Client;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBillsCharge;
use App\Models\Tenant\VehicleBillsDetail;
use App\Models\Tenant\VehicleBillsSetting;
use App\Models\Tenant\VehicleBillsSpend;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleEntity;
use App\Models\Tenant\VehicleType;
use App\Services\InjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleBillController extends Controller
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

    #-------------------------
    #     SETTING METHODS
    #-------------------------
    
    /**
     * View Page
     *
    */
    public function viewSettings()
    {
        return view('Tenant.vehicle.bills.setting.view');
    }

    /**
     * Create Page of Setting
     *
    */
    public function showSettingForm($config=null)
    {
        $settings = VehicleBillsSetting::all();


        return view('Tenant.vehicle.bills.setting.create', compact('config', 'settings'));
    }

    /**
     * POST request of creating the Setting
     *
    */
    public function create_setting(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:vehicle_bills_settings|max:255',
        ]);

        $setting = new VehicleBillsSetting;
        $setting->title = $request->get('title');
        $setting->grouped = $request->has('grouped');
        $setting->charged_is_spend = $request->has('charged_is_spend');
        $setting->includes_platecode = $request->has('includes_platecode');
        $setting->logic = $request->get('logic');

        $setting->save();

        return response()->json([
            'status' => 1
        ]);

    }

     /**
     * GET request of editing the page
     *
    */
    public function showSettingEditForm($id)
    {
        # Find the job
        $setting = VehicleBillsSetting::find($id);

        $setting->actions=[
            'status'=>1,
        ];

        # Call the load job function
        return $this->showSettingForm((object)[
            'setting'=>$setting,
            'action'=>'edit'
        ]);

    }

    public function edit_setting(Request $request)
    {
        $setting = VehicleBillsSetting::findOrFail($request->setting_id);

        $request->validate([
            'title' => [
                'required',
                'max:255',
                Rule::unique('vehicle_bills_settings')->ignore($setting->_id, '_id'),
            ]
        ]);


        $setting->title = $request->get('title');
        $setting->grouped = $request->has('grouped');
        $setting->charged_is_spend = $request->has('charged_is_spend');
        $setting->includes_platecode = $request->has('includes_platecode');
        $setting->logic = $request->get('logic');

        $setting->update();

        return response()->json([
            'status' => 1
        ]);
    }

    public function showBreakDownView(Request $request, int $ledger_id)
    {
        $ledger = Ledger::findOrFail($ledger_id);

        // Fetch vehicle_spends,vehicle_charged,vehicle_details
        $relations = $ledger
        ->relations()
        ->whereIn('source_model', [VehicleBillsSpend::class, VehicleBillsCharge::class, VehicleBillsDetail::class])
        ->get();

        // return $relations;

        
        // Attach driver/vehicle
        $vehicle_ids = $relations->pluck('source.vehicle_id')->unique()->filter(fn($item) => isset($item))->values();
        $driver_ids = $relations->pluck('source.driver_id')->unique()->filter(fn($item) => isset($item))->values();

        $drivers = Driver::whereIn('id', $driver_ids)->get();
        $vehicles = Vehicle::whereIn('id', $vehicle_ids)->get();

        $relations = $relations->map(function($item) use ($vehicles, $drivers, $relations){
            
            if(isset($item->source)){

                $item->source->vehicle = null;
                if(isset($item->source->vehicle_id)){
                    // Map vehicle to source
                    $vehicle = $vehicles->firstWhere('id', $item->source->vehicle_id);
                    $item->source->vehicle = $vehicle;
                }

                $item->source->driver = null;
                if(isset($item->source->driver_id)){
                    // Map vehicle to source
                    $driver = $drivers->firstWhere('id', $item->source->driver_id);
                    $item->source->driver = $driver;
                }

                $item->source->details = collect([]);
                if(isset($item->source->refs) && count($item->source->refs) > 0){
                    // Append all details here
                    $item->source->details = $relations->where('source_model', VehicleBillsDetail::class)->whereIn('source.ref', $item->source->refs)->pluck('source')->values();
                    
                }
            }

            return $item;
        });

        $spends = $relations->where('source_model', VehicleBillsSpend::class)->pluck('source')->values();
        $charged = $relations->where('source_model', VehicleBillsCharge::class)->pluck('source')->values();
        $details = $relations->where('source_model', VehicleBillsDetail::class)->pluck('source')->values();

        $total_spend = $spends->sum('amount');
        $total_charged = $charged->sum('amount');

        // return compact('charged');

        return view('Tenant.vehicle.bills.breakdown.view', compact('spends', 'charged', 'details', 'total_spend', 'total_charged', 'ledger'));
    }
}
