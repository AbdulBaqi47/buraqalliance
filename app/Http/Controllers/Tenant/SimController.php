<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Models\Tenant\Sim;
use App\Models\Tenant\SimEntity;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\User;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SimController extends Controller
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

    /**
     * View Page of sims
     *
    */
    public function ViewSims()
    {
        return view('Tenant.sim.view');
    }

    /**
     * Create view Page of sim addition
     *
    */
    public function showSimForm($config=null)
    {
        return view('Tenant.sim.create', compact('config'));
    }

    /**
     * POST request of creating the sim
     *
    */
    public function create_sim(Request $request)
    {
        $request->merge(['number' => trim($request->number)]);
        $request->validate([
            'number' => 'required|numeric|unique:sims',
            'company' => 'required|in:du,etisalat|max:255',
            'type' => 'required|in:prepaid,postpaid|max:255',
        ]);

        $sim = new Sim;
        $sim->number = trim($request->number);
        $sim->company = $request->company;
        $sim->type = $request->type;
        $sim->purchasing_date = isset($request->purchasing_date) ? Carbon::parse($request->purchasing_date)->format('Y-m-d') : null;
        $sim->save();

        $sim->actions=[
            'status'=>1,
        ];

        return response()->json($sim);
    }


    /**
     * GET request of editing the page
     *
    */
    public function showSimEditForm(int $id)
    {
        # Find the job
        $sim = Sim::findOrFail($id);
        $sim->actions=[
            'status'=>1,
        ];

        # Call the load job function
        return $this->showSimForm((object)[
            'sim'=>$sim,
            'action'=>'edit'
        ]);

    }

    public function edit_sim(Request $request, int $sim_id)
    {

        $sim = Sim::findOrFail($sim_id);
        $request->merge(['number' => trim($request->number)]);
        $request->validate([
            'number' => [
                'required',
                'numeric',
                Rule::unique('sims')->ignore($sim->_id, '_id'),
            ],
            'type' => 'required|in:prepaid,postpaid|max:255',
            'company' => 'required|in:du,etisalat|max:255',
        ]);

        $sim->number = $request->number;
        $sim->company = $request->company;
        $sim->type = $request->type;
        $sim->purchasing_date = isset($request->purchasing_date) ? Carbon::parse($request->purchasing_date)->format('Y-m-d') : null;
        $sim->update();

        $sim->actions=[
            'status'=>1,
        ];

        return response()->json($sim);
    }

    // ------------------------------------------
    //      Sim Entites Methods
    // ------------------------------------------


    /**
     * View Page of sim entites
     *
    */
    public function ViewSimEntities(int $id)
    {
        $sim = Sim::with('entities.source')
        ->findOrFail($id);

        // if($sim->source === 'booking'){
        //     $sim->load([
        //         'entities.source.vehicle'
        //     ]);
        // }

        $activeEntities = $sim->entities->where('status', 'active')->values();
        $inActiveEntities = $sim->entities->where('status', 'inactive')->values();

        return view('Tenant.sim.entities.view', compact('sim', 'activeEntities', 'inActiveEntities'));
    }

    /**
     * Create view Page of sim entities addition
     *
    */
    public function showSimEntitiesForm(int $id, $config=null)
    {

        $sim = Sim::findOrFail($id);

        # All possible sources a sim can assigned to
        $sources = [
            'driver' => [],
            'staff' => []
        ];

        # Source: Drivers
        $sources['driver'] = Driver::select('id', 'name')->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => $item->full_name,
                'selected' => (int)old('source_id') === (int)$item->id
            ];
        });


        # Source: Staff / Employee
        $sources['staff'] = User::select('_id', 'name')
        ->employees()
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        $entities = SimEntity::where('sim_id', $id)->with('source')->get();

        return view('Tenant.sim.entities.create', compact('config', 'sources', 'sim', 'entities'));
    }

    /**
     * POST request of creating the sim entity
     *
    */
    public function create_entity(int $id, Request $request)
    {
        $sim = Sim::with([
            'entities' => function($query){
                $query->whereNull('unassign_date');
            }
        ])->findOrFail($id);

        $request->validate([
            'assign_date' => 'required|date',
            'unassign_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'allowed_balance' => 'required|numeric',
        ]);

        $sourceModel = $request->source_type === "driver" ? Driver::class : User::class;

        $unassignDate = $request->get('unassign_date', null);


        $entity = new SimEntity;
        $entity->sim_id = $sim->id;
        $entity->source_id = $request->source_type === "driver" ? (int) $request->source_id : $request->source_id;
        $entity->source_model = $sourceModel;
        $entity->allowed_balance = intval($request->allowed_balance);
        $entity->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $entity->contract_end_date = Carbon::parse($request->contract_end_date)->format('Y-m-d');
        $entity->unassign_date = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;
        $entity->save();
        // if(isset($entity->unassign_date) && Carbon::parse($entity->unassign_date)->isBefore( Carbon::parse($request->contract_end_date) )){
        //     $this->charge_amount_before_contract_end($entity);
        // }


        return redirect()->route('tenant.admin.sims.entities.view', $sim->id);
    }

    /**
     * GET request of editing the page - AllowedBalance
     *
    */
    public function showEntitiesEditAllowedBalanceForm($id)
    {
        $entity = SimEntity::findOrFail($id);

        # Call the load function
        return $this->showSimEntitiesForm($entity->sim_id, (object)[
            'entity'=>$entity,
            'action'=>'allowed_balance'
        ]);
    }

    /**
     * GET request of editing the page - Contract date
     *
    */
    public function showEntitiesEditContractDateForm($id)
    {
        $entity = SimEntity::findOrFail($id);

        # Call the load function
        return $this->showSimEntitiesForm($entity->sim_id, (object)[
            'entity'=>$entity,
            'action'=>'contract_date'
        ]);
    }

    /**
     * GET request of editing the page - DATES
     *
    */
    public function showEntitiesEditDatesForm($id)
    {
        $entity = SimEntity::findOrFail($id);

        # Call the load function
        return $this->showSimEntitiesForm($entity->sim_id, (object)[
            'entity'=>$entity,
            'action'=>'dates'
        ]);
    }

    public function edit_entities($id, Request $request)
    {

        $sim_entity_id = $request->get('sim_entity_id');
        $entity = SimEntity::findOrFail($sim_entity_id);
        $entity_already_unassigned = isset($entity->unassign_date);

        $request->validate([
            'action' => 'required|in:allowed_balance,dates,contract_date',
        ]);

        $action = $request->get('action', null) ?? null;

        $msg = '';

        if($action === 'allowed_balance'){
            # Edit allowed_balance only

            $request->validate([
                'allowed_balance' => 'required|numeric',
            ]);


            $allowed_balance = $request->get('allowed_balance', null) ?? null;

            $msg .= "Allowed Balance: $entity->allowed_balance --> $allowed_balance";

            $entity->allowed_balance = $allowed_balance;
            $entity->notes = $request->notes;
            $entity->update();

        }
        else if($action === 'contract_date'){
            # Edit contract_date only

            $request->validate([
                'contract_end_date' => 'required|date',
            ]);


            $contract_end_date = $request->get('contract_end_date', null) ?? null;

            $msg .= "Contract End Date: $entity->contract_end_date --> $contract_end_date";

            $entity->contract_end_date = Carbon::parse($contract_end_date)->format('Y-m-d');;
            $entity->notes = $request->notes;
            $entity->update();

        }
        else if($action === 'dates'){
            # Edit Assign/Unassign Dates only

            $request->validate([
                'assign_date' => 'required|date',
                'unassign_date' => 'nullable|date',
            ]);

            $unassignDate = $request->get('unassign_date', null);
            $unassignDate = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;

            $assignDate = Carbon::parse($request->assign_date)->format('Y-m-d');

            if($unassignDate !== $entity->unassign_date){
                $msg .= "Un-Assign Date: ".Carbon::parse($entity->unassign_date)->format('Y-m-d')." --> $unassignDate";
            }

            if($assignDate !== $entity->assign_date){
                $msg .= ($msg !== '' ? "<br/>":'') . "Assign Date: ".Carbon::parse($entity->assign_date)->format('Y-m-d')." --> $assignDate";
            }


            $entity->assign_date = $assignDate;
            $date = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;
            $entity->unassign_date = $date;
            $entity->notes = $request->notes;
            $entity->update();
            // if(isset($date) && !$entity_already_unassigned && (Carbon::parse($date)->isBefore( Carbon::parse($entity->contract_end_date)) ) && isset($entity->contract_end_date)){
            //     $this->charge_amount_before_contract_end($entity);
            // }
        }
        return response()->json([
            'msg' => "Changes Saved! <br/>".$msg
        ]);

    }

    public function charge_amount_before_contract_end($entity){
        $source = $entity->source;
        $sim = $entity->sim;
        $total_amount = (float) $entity->allowed_balance;
        $month = Carbon::now()->startOfMonth();
        $date = Carbon::now();

        if( $total_amount <= 0)return;

        $title = "Sim Early Exit Charges";
        $description = "Sim: $sim->number
        Unassigned On: ".Carbon::parse($entity->unassign_date)->format('d/m/Y')."
        Contract Ends On: ".Carbon::parse($entity->contract_end_date)->format('d/m/Y')." ";

        $ledger_prefix = null;

        if($entity->source_model === User::class){
            $source_ledger = new Employee_ledger;
            $source_ledger->type = 'dr';
            $source_ledger->tag = "simbill_latecharge";
            $source_ledger->title = $title;
            $source_ledger->description = $description;
            $source_ledger->month = $month->format('Y-m-d');
            $source_ledger->date = $date->format('Y-m-d');
            $source_ledger->is_cash = false;
            $source_ledger->user_id = $source->_id;
            $source_ledger->amount = $total_amount;
            $source_ledger->save();

            $ledger_prefix = [
                'text' => $source->name,
                'url' => route('tenant.admin.employee.ledger.view').'?m='.$month->format("Y-m-d").'&e='.$source->id
            ];
        }
        else{
            $namespace = "booking";
            $driver_id = null;
            $group = 'sim'.$sim->number;
            if($entity->source_model == Driver::class){
                $resource_id = $source->booking_id;
                $driver_id = $source->id;
                $group .= '_driver'.$driver_id;

                $ledger_prefix = [
                    'text' => $source->full_name,
                    'url' => route('tenant.admin.vehicleledger.booking.view', $resource_id).'?t=month&v='.$month->format("Y-m-d")
                ];
            }
            else if($entity->source_model == VehicleBooking::class){
                $resource_id = $source->id;
                $group .= '_booking'.$source->id;

                $ledger_prefix = [
                    'text' => '#'.$source->id . ' / ' . $source->vehicle->plate,
                    'url' => route('tenant.admin.vehicleledger.booking.view', $resource_id).'?t=month&v='.$month->format("Y-m-d")
                ];
            }
            if(!isset($resource_id)){
                throw ValidationException::withMessages(["1" => "Source not assigned to booking - SIM ".$sim->number]);
                return;
            }

            $resource_id = (int) $resource_id;
            $vLedger = VehicleLedger::ofNamespace($namespace, $resource_id)->get()->first();
            $vItemObj = (object)[
                'title' => $title,
                'description' => $description,
                'type' => "dr",
                'group' => $group,
                'tag' => "simbill_latecharge",
                'channel' => "app",
                'date' => $date->format('Y-m-d'),
                'month' => $month->format('Y-m-d'),
                'amount' => $total_amount,
                'attachment' => null,
                'additional_details' => [
                    'sim_number' => $sim->number,
                    'total_amount' => $total_amount,
                    'assign_date' => Carbon::parse($entity->assign_date)->format('Y-m-d'),
                    'unassign_date' => Carbon::parse($entity->unassign_date)->format('Y-m-d'),
                    'contract_end_date' => Carbon::parse($entity->contract_end_date)->format('Y-m-d')
                ]
            ];
            if(isset($driver_id)){
                $vItemObj->driver_id = $driver_id;
            }
            $source_ledger =  $vLedger->addItem($vItemObj);
        }


        // # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$source_ledger->id;
        $ledger->source_model=get_class($source_ledger);
        $ledger->date=$date->format('Y-m-d');
        $ledger->tag="simbill_latecharge";
        $ledger->month = $source_ledger->month; // For Filteration Purpose
        $ledger->is_cash= false;
        $ledger->amount = $total_amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
            'prefix' => $ledger_prefix
        ];
        $ledger->save();

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $source_ledger->id;
        $relation->source_model = get_class($source_ledger);
        $relation->tag = 'simbill_latecharge';
        $relation->is_real = true;
        $relation->save();
    }


    public function delete_entities($id)
    {
        $entity = SimEntity::findOrFail($id);

        $entity->delete();

        return response()->json([
            "status" => 1
        ]);
    }




}
