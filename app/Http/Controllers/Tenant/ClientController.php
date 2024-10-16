<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

use App\Models\Tenant\Client;
use App\Models\Tenant\ClientEntities;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use Illuminate\Validation\Rule;

class ClientController extends Controller
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
     * View Page of clients
     *
    */
    public function ViewClients($type)
    {
        return view('Tenant.client.view', compact('type'));
    }

    
    /**
     * View Page of clients - Aggregator
     *
    */
    public function ViewClientsAggregator()
    {
        return $this->ViewClients('aggregator');
    }
    
    /**
     * View Page of clients - Supplier
     *
    */
    public function ViewClientsSupplier()
    {
        return $this->ViewClients('supplier');
    }

    /**
     * Create view Page of client addition
     *
    */
    public function showClientForm($config=null)
    {
        return view('Tenant.client.create', compact('config'));
    }

    /**
     * POST request of creating the client
     *
    */
    public function create_client(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'source' => 'required|in:driver,vehicle|max:255',
            'email' => 'required|unique:clients|max:255',
        ]);

        $client = new Client;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->source = $request->source;
        $client->monthly_rent = null;
        $client->start_date = null;
        $client->end_date = null;

        if($request->source === 'vehicle'){
            $client->monthly_rent = $request->monthly_rent;
            $client->start_date = $request->start_date;
            $client->end_date = $request->end_date;
        }

        $client->trn = $request->trn ?? null;
        $client->address = $request->address ?? null;
        $client->status = 'active';
        $client->save();

        $client->open_balance = 0;
        $client->actions=[
            'status'=>1,
        ];

        return response()->json($client);
    }


    /**
     * GET request of editing the page
     *
    */
    public function showClientEditForm($id)
    {
        # Find the job
        $client = Client::find((int)$id);
        $client->actions=[
            'status'=>1,
        ];

        # Call the load job function
        return $this->showClientForm((object)[
            'client'=>$client,
            'action'=>'edit'
        ]);

    }

    public function edit_client(Request $request)
    {

        $client = Client::findOrFail((int)$request->client_id);


        $request->validate([
            'client_id' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => [
                'required',
                'max:255',
                Rule::unique('clients')->ignore($client->_id, '_id'),
            ],
        ]);

        $client->name = $request->name;
        $client->email = $request->email;
        if($client->source === 'vehicle'){
            $client->monthly_rent = $request->monthly_rent;
            $client->start_date = $request->start_date;
            $client->end_date = $request->end_date;
        }
        $client->trn = $request->trn;
        $client->address = $request->address;
        $client->status = 'active';
        $client->update();

        $client->open_balance=0;
        $client->actions=[
            'status'=>1,
        ];

        return response()->json($client);
    }

    // ------------------------------------------
    //      Client Entites Methods
    // ------------------------------------------


    /**
     * View Page of client entites
     *
    */
    public function ViewClientEntities(int $id)
    {
        $client = Client::with('entities.source')
        ->findOrFail($id);

        if($client->source === 'vehicle'){
            $client->load([
                'entities.source.vehicle'
            ]);
        }

        $activeEntities = $client->entities->where('status', 'active')->values();
        $inActiveEntities = $client->entities->where('status', 'inactive')->values();

        return view('Tenant.client.entities.view', compact('client', 'activeEntities', 'inActiveEntities'));
    }

    /**
     * Create view Page of client entities addition
     *
    */
    public function showClientEntitiesForm(int $id, $config=null)
    {

        $client = Client::findOrFail($id);

        if($client->source === 'driver'){
            $sources = Driver::all()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'text' => $item->full_name,
                    'metadata' => $item,
                    'selected' => (int)old('source_id') === (int)$item->id
                ];
            });
        }
        else{
            $sources = Vehicle::select('id','plate')
            // ->where('status', 'closed')
            ->get()
            ->map(function($item){
                $text='';
                if(isset($item)){
                    $text = 'V#'.$item->id.' / L-'.$item->plate;
                }
                return [
                    'id' => $item->id,
                    'text' => $text,
                    'selected' => (int)old('source_id') === (int)$item->id
                ];
            });
        }
        $entities = ClientEntities::where('client_id', $id)->with('source')->get();

        return view('Tenant.client.entities.create', compact('config', 'sources', 'client', 'entities'));
    }

    /**
     * POST request of creating the client entity
     *
    */
    public function create_entity(int $id, Request $request)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'assign_date' => 'required|date',
            'unassign_date' => 'nullable|date',
        ]);

        $sourceModel = $client->source === "driver" ? Driver::class : Vehicle::class;
        $unassignDate = $request->get('unassign_date', null);

        if($client->source === "driver"){
            $request->validate([
                'refid' => 'nullable|max:255',
            ]);
            # Check if RefID is unique between sources
            $refid = $request->get('refid', null) ?? null;
            # ------------------
            #  Validate Driver
            # ------------------
            $driver = Driver::find((int) $request->source_id);
            if(isset($driver)){
                $valid = $driver->isDocumentsUploaded();
                if(!$valid){
                    throw ValidationException::withMessages(['Error' => 'Missing fields found in driver ('.$driver->full_name.') that needs to be filled before assigning']);
                    return;
                }
            }
        }

        $entity = new ClientEntities;
        $entity->client_id = $client->id;
        $entity->source_id = (int) $request->source_id;
        $entity->source_model = $sourceModel;
        if($client->source === "driver"){
            $entity->refid = $refid;
        }else{
            $entity->monthly_rent = null;
            if(isset($request->monthly_rent)){
                $entity->monthly_rent = (int) $request->monthly_rent;
            }
        }
        $entity->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $entity->unassign_date = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;
        $entity->notes = $request->notes;
        $entity->save();


        return redirect()->route('tenant.admin.clients.entities.view', $client->id)->with('message', "$client->source $entity->source_id is assigned $client->name successfully!");
    }


    /**
     * GET request of editing the page - Monthly Rent
     *
    */
    public function showEntitiesEditMonthlyRentForm($id)
    {
        $entity = ClientEntities::findOrFail($id);
        # Call the load function
        return $this->showClientEntitiesForm($entity->client_id, (object)[
            'entity'=>$entity,
            'action'=>'monthly_rent'
        ]);
    }
    public function showEntitiesEditRefIDForm($id)
    {
        $entity = ClientEntities::findOrFail($id);

        # Call the load function
        return $this->showClientEntitiesForm($entity->client_id, (object)[
            'entity'=>$entity,
            'action'=>'refid'
        ]);
    }

    /**
     * GET request of editing the page - DATES
     *
    */
    public function showEntitiesEditDatesForm($id)
    {
        $entity = ClientEntities::findOrFail($id);

        # Call the load function
        return $this->showClientEntitiesForm($entity->client_id, (object)[
            'entity'=>$entity,
            'action'=>'dates'
        ]);
    }

    public function edit_entities($id, Request $request)
    {

        $client_entity_id = $request->get('client_entity_id');
        $entity = ClientEntities::findOrFail($client_entity_id);

        $request->validate([
            'action' => 'required|in:refid,monthly_rent,dates',
        ]);

        $action = $request->get('action', null) ?? null;

        $msg = '';

        if($action === 'monthly_rent'){
            # Edit Monthly Rent only

            $request->validate([
                'monthly_rent' => 'nullable|max:255',
            ]);

            $monthly_rent = $request->get('monthly_rent', null) ?? null;
            if(isset($monthly_rent)) $monthly_rent = (int) $monthly_rent;
            
            $entity->monthly_rent = $monthly_rent;
        }
        else if($action === 'refid'){
            # Edit RefID only

            $request->validate([
                'refid' => 'required|max:255',
            ]);


            $exists = ClientEntities::where('refid', $request->refid)
            ->where('source_id', '!=', $entity->source_id)
            ->where('client_id', $id)
            ->where('source_model', $entity->source_model)
            ->exists();

            if($exists){
                throw ValidationException::withMessages(['RefID' => 'A record was found with similer data']);
                return;
            }

            $refid = $request->get('refid', null) ?? null;

            $msg .= "RefID: $entity->refid --> $refid";

            $entity->refid = $refid;

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
            $entity->unassign_date = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;

        }


        $entity->notes = $request->notes;
        $entity->update();

        return response()->json([
            'msg' => "Changes Saved! <br/>".$msg
        ]);


    }


    public function delete_entities($id)
    {
        $entity = ClientEntities::findOrFail($id);

        $entity->delete();

        return response()->json([
            "status" => 1
        ]);
    }




}
