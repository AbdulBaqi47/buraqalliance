<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Vehicle;
use App\Services\InjectService;
use App\Models\Tenant\VehicleLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tenant_auth');
    }

    function showVisas()
    {
        return view('Tenant.visa.all');
    }

    function addVisaExpense(Request $request)
    {
        $drivers = Driver::select('id', 'name')
        ->get()
        ->map(function ($item) use ($request) {
            return [
                'id' => $item->id,
                'text' => $item->full_name,
                'selected' => intval($request->id) == $item->id
            ];
        });
        return view('Tenant.drivers.actions.charge_driver_visa', compact('drivers'));
    }

    private function addExpenseAdditionalData(VehicleLedger $ledger, Request $request)
    {
        $ledger->save();
        $ledger->addItem((object)[
            'title' => "Visa Installment Amount",
            'description' => "",
            'tag' => 'driver_visa',
            'type' => 'cr',
            'date' => $request->date,
            'month' => Carbon::parse($request->date)->startOfMonth()->format('Y-m-d'),
            'amount' => $request->amount
        ]);
        $ledger->save();
        return $ledger;
    }

    function storeVisaExpense(Request $request)
    {
        $helper_service = new InjectService();

        $request_type = $helper_service->helper->request_type();

        $driver  = Driver::where('id', intval($request->driver_id))->with(['investor', 'vehicle', 'booking'])->first();
        $ledger = new VehicleLedger;
        if (isset($driver['booking'])) {
            $ledger->vehicle_booking_id = $driver->booking_id;
            $this->addExpenseAdditionalData($ledger, $request);
        } else if (isset($driver['vehicle'])) {
            $ledger->vehicle_id = $driver->vehicle_id;
            $this->addExpenseAdditionalData($ledger, $request);
        } else {
            if($request_type === 'ajax'){
                return response()->json(['success'=>false, 'message' => 'Assign Booking or Vehicle to Driver'], 404);
            }else{
                return back()->with('message','Assign Booking or Vehicle to Driver');
            }
        }
        if($request_type === 'ajax'){
            return response()->json(['success'=>true, 'message' => "Visa Charge from $driver->full_name Created Successfully"]);
        }
        return back()->with('message',"Visa Charge from $driver->full_name Created Successfully");
    }
}
