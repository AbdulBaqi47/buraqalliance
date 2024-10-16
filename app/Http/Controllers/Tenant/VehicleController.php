<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Tenant\Bike;
use App\Models\Tenant\Client;
use App\Models\Tenant\ClientEntities;
use App\Models\Tenant\Driver;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBillsSetting;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleEntity;
use App\Models\Tenant\VehicleType;
use App\Services\InjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
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
     * View Page of Vehcile
     *
    */
    public function ViewVehicle($type)
    {
        return view('Tenant.vehicle.view', compact('type'));
    }
    /**
     * View Page of Vehicles - Vehicle
     *
    */
    public function ViewVehiclesVehicle()
    {
        return $this->ViewVehicle('vehicle');
    }

    /**
     * View Page of clients - Supplier
     *
    */
    public function ViewVehiclesBike()
    {
        return $this->ViewVehicle('bike');
    }

    /**
     * Create view Page of client addition
     *
    */
    public function showVehicleForm(request $request, $config=null)
    {
        $clients = Client::where('source','vehicle')->select('id', 'name','monthly_rent')->get();

        // Unique Modal/PlateCode
        $groupedData = Vehicle::select('model', 'plate_code')
        ->get();

        $models = $groupedData
        ->map(function($item){
            $item->model = strtolower($item->model);
            return $item;
        })
        ->keyBy('model')
        ->keys()
        ->map(function($item){
            return ucwords($item);
        });

        $plateCodes = $groupedData
        ->map(function($item){
            $item->plate_code = strtolower($item->plate_code);
            return $item;
        })
        ->keyBy('plate_code')
        ->keys()
        ->map(function($item){
            return ucwords($item);
        });
        $type= $request->type;

        return view('Tenant.vehicle.create', compact( 'clients', 'models', 'plateCodes', 'config', 'type'));
    }

    /**
     * POST request of creating the client
     *
    */
    public function create(request $request)
    {
        // Before validation, cast the integer values, otherwise validation may not work as expected
        $request->merge(['rental_company' => (int)$request->rental_company]);

        $request->validate([
            'type' => 'required',
            'plate' => 'nullable|unique:vehicles|max:255',
            'year' => 'required|max:255',
            'chassis_number' => 'required|unique:vehicles|max:255',
            'engine_number' => 'required|unique:vehicles|max:255',
            'rental_company' => 'required|exists:clients,id|max:255',
            'rental_company_month' => 'required|max:255',
            'location' => 'required|max:255',
        ]);


        $vehicle = new Vehicle;
        $vehicle->chassis_number=$request->chassis_number;
        $vehicle->engine_number=$request->engine_number;
        $vehicle->type=$request->type;
        $vehicle->model=ucwords(strtolower($request->model));
        $vehicle->year=$request->year;
        $vehicle->color=strtolower(trim($request->color));
        $vehicle->plate=$request->plate;
        $vehicle->plate_code=ucwords(strtolower($request->plate_code));
        $vehicle->state=$request->state;
        $vehicle->location=$request->location;
        $vehicle->insurance_company=$request->insurance_company;
        $vehicle->branding_type=$request->branding_type;
        $vehicle->have_bagbox=$request->have_bagbox;
        $vehicle->notes="";

        $mulkiya_pictures = [];
        $advertisement_attachment = null;
        $insurance_paper_attachment = null;
        if($request->hasFile('mulkiya_picture_front')){
            $mulkiya_pictures['front'] = Storage::putfile('vehicles/mulkiya_pictures/front', $request->file('mulkiya_picture_front'));
        }
        if($request->hasFile('mulkiya_picture_back')){
            $mulkiya_pictures['back'] = Storage::putfile('vehicles/mulkiya_pictures/back', $request->file('mulkiya_picture_back'));
        }
        if($request->hasFile('insurance_paper_attachment')){
            $insurance_paper_attachment = Storage::putfile('vehicles/insurance_paper_attachment', $request->file('insurance_paper_attachment'));
        }
        if($request->hasFile('advertisement_attachment')){
            $advertisement_attachment = Storage::putfile('vehicles/advertisement_attachment', $request->file('advertisement_attachment'));
        }

        $vehicle->mulkiya_pictures=count($mulkiya_pictures) === 0 ? null : $mulkiya_pictures;
        $vehicle->advertisement_attachment=$advertisement_attachment;
        $vehicle->insurance_paper_attachment=$insurance_paper_attachment;
        $vehicle->mulkiya_expiry=  isset($request->mulkiya_expiry) ? Carbon::parse($request->mulkiya_expiry)->format('Y-m-d') : '';
        $vehicle->insurance_expiry= isset($request->insurance_expiry) ? Carbon::parse($request->insurance_expiry)->format('Y-m-d') : '';
        $vehicle->insurance_issue_date= isset($request->insurance_issue_date) ? Carbon::parse($request->insurance_issue_date)->format('Y-m-d') : '';
        $vehicle->insurance_paper_issue_date= isset($request->insurance_paper_issue_date) ? Carbon::parse($request->insurance_paper_issue_date)->format('Y-m-d') : '';
        $vehicle->insurance_paper_expiry_date= isset($request->insurance_paper_expiry_date) ? Carbon::parse($request->insurance_paper_expiry_date)->format('Y-m-d') : '';
        $vehicle->advertisement_issue_date= isset($request->advertisement_issue_date) ? Carbon::parse($request->advertisement_issue_date)->format('Y-m-d') : '';
        $vehicle->advertisement_expiry_date= isset($request->advertisement_expiry_date) ? Carbon::parse($request->advertisement_expiry_date)->format('Y-m-d') : '';

        $vehicle->save();

        # ----------------------
        # Create vehicle entity
        # ----------------------
        $monthly_rent = isset($request->monthly_rent) ? (int)$request->monthly_rent : null;

        $vehicleClientEntity= new ClientEntities;
        $vehicleClientEntity->client_id = $request->rental_company;
        $vehicleClientEntity->source_id = $vehicle->id;
        $vehicleClientEntity->source_model = Vehicle::class;
        $vehicleClientEntity->monthly_rent = $monthly_rent;
        $vehicleClientEntity->assign_date = Carbon::parse($request->rental_company_month)->startOfMonth()->format('Y-m-d');
        $vehicleClientEntity->unassign_date = null;
        $vehicleClientEntity->save();

        # This service will help us
        $helper_service = new InjectService();

        $request_type = $helper_service->helper->request_type();

        if($request_type === 'ajax'){

            $vResponse = (object)$vehicle->toArray();
            $vResponse->actions=[
                'status'=>1,
            ];

            return response()->json($vResponse);
        }
        $type = $request->type== 'vehicle' ? 'Vehicle' : 'Bike';
        return back()->with('message', "$type created successfully!");


    }

    /**
     * GET request of editing the page
     *
    */
    public function showEditForm(request $request, $id)
    {
        $vehicle = Vehicle::with([
            'vehicle_client_entities' => function($query){
                $query->limit(1);
            }
        ])->findOrFail((int)$id);
        $vehicle->actions=[
            'status'=>1,
        ];

        # Call the load function
        return $this->showVehicleForm($request, (object)[
            'vehicle'=>$vehicle,
            'vehicleClient'=>count($vehicle->vehicle_client_entities) > 0 ? $vehicle->vehicle_client_entities->first() : null,
            'action'=>'edit'
        ]);

    }

    public function edit(Request $request)
    {

        $vehicle = Vehicle::findOrFail((int)$request->vehicle_id);

        $request->validate([
            'plate' => [
                'nullable',
                'max:255',
                Rule::unique('vehicles')->ignore($vehicle->_id, '_id'),
            ],
            // 'chassis_number' => [
            //     'required',
            //     'max:255',
            //     Rule::unique('vehicles')->ignore($vehicle->_id, '_id'),
            // ],
            // 'engine_number' => [
            //     'required',
            //     'max:255',
            //     Rule::unique('vehicles')->ignore($vehicle->_id, '_id'),
            // ],
            // 'color' => 'required|max:255',
            // 'state' => 'required|max:255'
        ]);



        $vehicle->chassis_number=$request->chassis_number;
        $vehicle->engine_number=$request->engine_number;
        $vehicle->type=$request->type;
        $vehicle->model=$request->model;
        $vehicle->year=$request->year;
        $vehicle->color=strtolower(trim($request->color));
        $vehicle->plate=$request->plate;
        $vehicle->plate_code=$request->plate_code;
        $vehicle->state=$request->state;
        $vehicle->location=$request->location;
        $vehicle->insurance_company=$request->insurance_company;
        $vehicle->branding_type=$request->branding_type;
        $vehicle->have_bagbox=$request->have_bagbox;
        $vehicle->notes="";

        $mulkiya_pictures = [];
        $insurance_paper_attachment = null;
        $advertisement_attachment = null;
        if($request->hasFile('mulkiya_picture_front')){
            // if(isset($vehicle->mulkiya_pictures) && isset($vehicle->mulkiya_pictures['front']))Storage::delete($vehicle->mulkiya_pictures['front']); # it will delete img from live too

            $mulkiya_pictures['front'] = Storage::putfile('vehicles/mulkiya_pictures/front', $request->file('mulkiya_picture_front'));
        }
        if($request->hasFile('mulkiya_picture_back')){
            // if(isset($vehicle->mulkiya_pictures) && isset($vehicle->mulkiya_pictures['back']))Storage::delete($vehicle->mulkiya_pictures['back']); # it will delete img from live too

            $mulkiya_pictures['back'] = Storage::putfile('vehicles/mulkiya_pictures/back', $request->file('mulkiya_picture_back'));
        }
        if($request->hasFile('insurance_paper_attachment')){
            $insurance_paper_attachment = Storage::putfile('vehicles/insurance_paper_attachment', $request->file('insurance_paper_attachment'));
        }
        if($request->hasFile('advertisement_attachment')){
            $advertisement_attachment = Storage::putfile('vehicles/advertisement_attachment', $request->file('advertisement_attachment'));
        }

        if((count($mulkiya_pictures) > 0) && isset($mulkiya_pictures)) $vehicle->mulkiya_pictures = $mulkiya_pictures;
        if(isset($insurance_paper_attachment))  $vehicle->insurance_paper_attachment = $insurance_paper_attachment;
        if(isset($advertisement_attachment))  $vehicle->advertisement_attachment = $advertisement_attachment;
        $vehicle->mulkiya_expiry= isset($request->mulkiya_expiry) ? Carbon::parse($request->mulkiya_expiry)->format('Y-m-d') : '';
        $vehicle->insurance_expiry= isset($request->insurance_expiry) ? Carbon::parse($request->insurance_expiry)->format('Y-m-d') : '';
        $vehicle->insurance_issue_date= isset($request->insurance_issue_date) ? Carbon::parse($request->insurance_issue_date)->format('Y-m-d') : '';
        $vehicle->insurance_paper_issue_date= isset($request->insurance_paper_issue_date) ? Carbon::parse($request->insurance_paper_issue_date)->format('Y-m-d') : '';
        $vehicle->insurance_paper_expiry_date= isset($request->insurance_paper_expiry_date) ? Carbon::parse($request->insurance_paper_expiry_date)->format('Y-m-d') : '';
        $vehicle->advertisement_issue_date= isset($request->advertisement_issue_date) ? Carbon::parse($request->advertisement_issue_date)->format('Y-m-d') : '';
        $vehicle->advertisement_expiry_date= isset($request->advertisement_expiry_date) ? Carbon::parse($request->advertisement_expiry_date)->format('Y-m-d') : '';

        $vehicle->update();



        # This service will help us
        $helper_service = new InjectService();

        $request_type = $helper_service->helper->request_type();

        if($request_type === 'ajax'){

            $vResponse = (object)$vehicle->toArray();
            $vResponse->actions=[
                'status'=>1,
            ];

            return response()->json($vResponse);
        }
        if($request->type === 'vehicle'){
            return redirect(route('tenant.admin.vehicles.vehicle.view'))->with('message', "Vehicle updated successfully!");
        }
        return redirect(route('tenant.admin.vehicles.bike.view'))->with('message', "Bike updated successfully!");

    }

    public function addBulk(Request $request) {
        $request->validate([
            'count' => ['gt:0','numeric']
        ]);
        $count = (int) $request->count;
        for ($i=0; $i < $count; $i++) {
            $vehicle = new Vehicle;
            $vehicle->save();
        }
        return response()->json(['message'=>'Added Successfully']);
    }
    public function ViewVehicleEntities(int $id)
    {
        $vehicle = Vehicle::with('entities.source')
        ->findOrFail($id);

        // if($vehicle->source === 'booking'){
        //     $vehicle->load([
        //         'entities.source.vehicle'
        //     ]);
        // }

        $activeEntities = $vehicle->entities->where('status', 'active')->values();
        $inActiveEntities = $vehicle->entities->where('status', 'inactive')->values();

        return view('Tenant.vehicle.entities.view', compact('vehicle', 'activeEntities', 'inActiveEntities'));
    }
    /**
     * Create view Page of Vehicle entities addition
     *
    */
    public function showVehicleEntitiesForm(int $id, $config=null)
    {

        $vehicle = Vehicle::findOrFail($id);

        # All possible sources a vehicle can assigned to
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

        $entities = VehicleEntity::where('vehicle_id', $id)->with('source')->get();
        return view('Tenant.vehicle.entities.create', compact('config', 'sources', 'vehicle', 'entities'));
    }

    public function create_entity(int $id, Request $request)
    {
        $vehicle = Vehicle::with([
            'entities' => function($query){
                $query->whereNull('unassign_date');
            }
        ])->findOrFail($id);

        $request->validate([
            'assign_date' => 'required|date',
            'unassign_date' => 'nullable|date',
            // 'contract_end_date' => 'nullable|date',
        ]);

        $sourceModel = $request->source_type === "driver" ? Driver::class :  User::class;
        $unassignDate = $request->get('unassign_date', null);

        if($vehicle->entities->count() > 0 && !isset($unassignDate)){
            throw ValidationException::withMessages(['Assign Entity' => 'Kindly Unassign Previous Assigned Entity First']);
        }
        $vehicle_assign_assessment_picture = null;

        $entity = new VehicleEntity;
        $entity->vehicle_id = $vehicle->id;
        $entity->source_id = $request->source_type === "driver" ? (int) $request->source_id : $request->source_id;
        $entity->source_model = $sourceModel;
        $entity->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $entity->unassign_date = isset($unassignDate) && $unassignDate != '' ? Carbon::parse($request->unassign_date)->format('Y-m-d') : null;

        if($request->hasFile('vehicle_assign_assessment_picture')){
            $vehicle_assign_assessment_picture = Storage::putfile('vehicles/vehicle_assign_assessment_picture', $request->file('vehicle_assign_assessment_picture'));
        }
        $entity->vehicle_assign_assessment_picture=$vehicle_assign_assessment_picture;

        $entity->save();

        return redirect()->route('tenant.admin.vehicles.entities.view', $vehicle->id);
    }
    public function delete_entities($id)
    {
        $entity = VehicleEntity::findOrFail($id);

        $entity->delete();

        return response()->json([
            "status" => 1
        ]);
    }
     /**
     * GET request of editing the page - DATES
     *
    */
    public function showEntitiesEditDatesForm($id)
    {
        $entity = VehicleEntity::findOrFail($id);

        # Call the load function
        return $this->showVehicleEntitiesForm($entity->vehicle_id, (object)[
            'entity'=>$entity,
            'action'=>'dates'
        ]);
    }
    /**
     * GET request of editing the page - DATES
     *
    */
    public function showEntitiesEditImgForm($id)
    {
        $entity = VehicleEntity::findOrFail($id);

        # Call the load function
        return $this->showVehicleEntitiesForm($entity->vehicle_id, (object)[
            'entity'=>$entity,
            'action'=>'img'
        ]);
    }

    public function edit_entities($id, Request $request)
    {

        $vehicle_entity_id = $request->get('vehicle_entity_id');
        $entity = VehicleEntity::findOrFail($vehicle_entity_id);

        $request->validate([
            'action' => 'required|in:dates,img',
        ]);

        $action = $request->get('action', null) ?? null;

        $msg = '';

        if($action === 'dates'){
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
        }
        if($action === 'img'){
            $request->validate([
                'vehicle_assign_assessment_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust as per your needs
            ]);
            // make edit store for Attachments
            $vehicle_assign_assessment_picture = null;

            if($request->hasFile('vehicle_assign_assessment_picture')){
                $vehicle_assign_assessment_picture = Storage::putfile('vehicles/vehicle_assign_assessment_picture', $request->file('vehicle_assign_assessment_picture'));
            }

            if($vehicle_assign_assessment_picture)  $entity->vehicle_assign_assessment_picture = $vehicle_assign_assessment_picture;
        }
        $entity->update();

        return response()->json([
            'msg' => "Changes Saved! <br/>".$msg
        ]);

    }
    public function show_rental_company_entities(int $id){
        $activeEntities= ClientEntities::where('source_model',Vehicle::class)
        ->whereNull('unassign_date')
        ->where('source_id',$id)
        ->with('client')
        ->limit(1)
        ->first();

        if(!isset($activeEntities)){
            abort(505, "No active rental company found!");
        }

        $clients = Client::where('source', 'vehicle')->select('id', 'name','monthly_rent')->get();

        return view('Tenant.vehicle.entities.rental_company_entities', compact( 'activeEntities','clients'));
    }
    public function change_rental_company_entities($id, Request $request)
    {
        #unassign active entity with assign date of next month and assign new entity with selected month
        $activeEntity= ClientEntities::findOrFail($request->_id);
        $activeEntity->unassign_date=Carbon::parse($request->assign_date)->startOfMonth()->format('Y-m-d');
        $activeEntity->update();

        $ClientEntities= new  ClientEntities;
        $ClientEntities->client_id =(int) $request->rental_company;
        $ClientEntities->source_id=(int) $request->source_id;
        $ClientEntities->source_model = Vehicle::class;
        $ClientEntities->monthly_rent = null;
        $ClientEntities->assign_date = Carbon::parse($request->assign_date)->startOfMonth()->format('Y-m-d');
        $ClientEntities->unassign_date = null;
        $ClientEntities->save();


        return response()->json([
            'msg' => "Changes Saved!"
        ]);

    }
}
