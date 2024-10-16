<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Addon;
use App\Models\Tenant\AddonDeduction;
use App\Models\Tenant\AddonExpense;
use App\Models\Tenant\AddonsSetting;
use Illuminate\Http\Request;

use App\Models\Tenant\Driver;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Services\Injected\RouteService;
use App\Services\InjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AddonsController extends Controller
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
     * View Page
     *
    */
    public function view($dept_name)
    {
        # Check if user has access to this department
        $access_data = [$dept_name];
        if($dept_name === "driving_license") $access_data = ["driving_license_dubai", "driving_license_sharjah"];

        if(!app('helper_service')->routes->has_custom_access('addon_department', $access_data)) return response(view("403"));

        return view('Tenant.addons.view', compact('dept_name'));
    }

    /**
     * Create Page
     *
    */
    public function showAddonsForm($config=null)
    {
        $sourceType = request()->get('type', 'driver');

        $sources = [];
        if($sourceType === "driver"){
            $sources = Driver::select('id', 'name')
            ->get()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'text' => $item->full_name
                ];
            });
        }
        else if($sourceType === "vehicle"){
            $sources = Vehicle::select('id', 'plate', 'chassis_number', 'vehicle_booking_id')
            ->with([
                'vehicle_booking' => function($query){
                    $query->select('id');
                }
            ])
            ->get()
            ->map(function($item){
                $text = $item->plate.' / '.$item->chassis_number;
                if(isset($item->vehicle_booking)){
                    $text = 'V#'.$item->vehicle_booking->id.' / '.$item->plate.' / '.$item->chassis_number;
                }

                return [
                    'id' => $item->id,
                    'type' => 'vehicle',
                    'text' => $text
                ];
            });

            $bookings = VehicleBooking::select('id')
            ->whereDoesntHave('vehicle')
            ->get()
            ->map(function($item){
                $text = 'B#'.$item->id;
                return [
                    'id' => $item->id,
                    'type' => 'booking',
                    'text' => $text
                ];
            });

            $sources = $sources->merge($bookings);
        }
        else if($sourceType === "staff"){
            $sources = User::select('_id', 'name')
            ->employees()
            ->get()
            ->map(function($item){
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        }


        $types = AddonsSetting::select('id', 'title', 'amount', 'source_required', 'source_type', 'categories', 'types')
        ->where('source_type', $sourceType)
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => $item->title,
                'metadata' => $item
            ];
        });

        $addons = Addon::where('source_type', $sourceType)
        ->select('id', 'setting_id', 'source_id')
        ->get();

        return view('Tenant.addons.create', compact('config', 'sources', 'types', 'addons'));
    }

    /**
     * POST request of creating the addon
     *
    */
    public function create(request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'source_type' => 'required|max:255'
        ]);

        $items = $request->get('items');
        $sourceType = $request->get('source_type');

        # -------------------------------------
        #   Validate Items
        #   - Same Addon cannot be added twice
        # -------------------------------------

        foreach ($items as $index => $item) {

            $sourceId = isset($item['source_id']) ? (int)$item['source_id'] : null;

            if(isset($sourceId)){
                $addonFound = Addon::where('source_id', $sourceId)
                ->select('id', 'source_id', 'setting_id')
                ->with([
                    'setting' => function($query){
                        $query->select('id', 'title');
                    }
                ])
                ->where('setting_id', $item['type_id'])
                ->where('source_type', $sourceType)
                ->get()
                ->first();

                if(isset($addonFound)){
                    throw ValidationException::withMessages(['Row #'.$index => "A record was found with similer data <br /> ID: $addonFound->id <br /> Title: ".$addonFound->setting->title." <br /> SourceID: $addonFound->source_id"]);
                    return;
                }
            }
        }

        # ----------------------
        #   Add Items As ADDONS
        # ----------------------

        foreach ($items as $item) {

            $addon = new Addon;

            $sourceId = null;
            if(isset($item['source_id'])){

                if( $sourceType === "staff" ){
                    $sourceId = $item['source_id'];
                }
                else{
                    $sourceId = (int)$item['source_id'];
                }

            }

            $sourceModel = null;
            $isDriver = false;
            if($sourceType === 'staff') $sourceModel = User::class;
            if($sourceType === 'vehicle') {
                $vehicleType = $item['vehicle_type'] ?? null;
                if(isset($vehicleType)){
                    $sourceModel = $vehicleType === 'booking' ? VehicleBooking::class : Vehicle::class;
                }
                else{
                    $sourceModel = Vehicle::class;
                }
            }
            if($sourceType === 'driver') {
                $sourceModel = Driver::class;
                $isDriver = true;
            };
            $addon->payment_status = 'pending';
            $addon->setting_id = $item['type_id'];
            $addon->date = Carbon::now()->format('Y-m-d');
            $addon->price = (float)$item['price']??0;
            $addon->cost = 0;
            $addon->source_type = $sourceType;
            $addon->source_model = $sourceModel;
            $addon->source_id = $sourceId;
            $addon->additional_details = $item['additional_details']??null;
            $addon->status = 'initiated';
            $addon->current_stage = null;

            # Check if addon is of Visa, set the status to pending_to_start
            $addon_setting = AddonsSetting::find($item['type_id']);
            if(isset($addon_setting) && $addon_setting->title === 'Visa'){
                $addon->status = 'pending_to_start';
            }

            $addon->override_types = null;
            if(isset($item['override_settings'])){
                # Override default setting
                $addon->override_types = collect($item['overrides'])
                ->map(function($type){
                    return [
                        'title' => $type['title'],
                        'display_title' => $type['display_title'],
                        'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                        'charge' => isset($type['charge']) ? true : false,
                    ];
                })
                ->toArray();
            }

            $addon->save();
            if($isDriver && isset($sourceId)){
                $driver = Driver::findOrFail($sourceId);
                $driver->status = 'initiated';
                $driver->save();
            }
        }


        return response()->json([
            'status' => 1,
            'request' => $request->all()
        ]);

    }


    /**
     * Create Page of Charge
     *
    */
    public function showChargeForm($id, $config=null)
    {
        $addon = Addon::findOrFail((int)$id)
        ->append('breakdown');


        return view('Tenant.addons.charge.create', compact('config', 'addon'));
    }

    /**
     * POST request of creating the charge
     *
    */
    public function create_charge($id, Request $request)
    {
        $addon = Addon::with([
            'setting' => function($query){
                $query->select('id', 'title');
            }
        ])

        ->findOrFail((int)$id);


        $request->validate([
            'resource_id' => 'required|max:255',
            'namespace' => 'required|max:255',
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'month' => 'required|date'
        ]);

        $amount= $request->has('amount') ? (float)$request->amount : 0;
        $date=Carbon::parse($request->date)->format('Y-m-d');
        $month=Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');
        $description = $request->get('description');

        if($addon->source_type === "staff"){
            // We need to charge it in employee ledger

            $user = User::find($request->resource_id);

            # --------------------------
            #   Add Employee Ledger
            # --------------------------

            #Add advance to Employee Ledger
            $vLedgerItem = new Employee_ledger;
            $vLedgerItem->type='dr';
            $vLedgerItem->tag='addon_charge';
            $vLedgerItem->title=$addon->setting->title . " Charge";
            $vLedgerItem->description=$description;
            $vLedgerItem->month=$month;
            $vLedgerItem->date=$date;
            $vLedgerItem->user_id=$request->resource_id;
            $vLedgerItem->is_cash=false;
            $vLedgerItem->amount=$amount;


            #need to check if image added
            $filepath = null;
            if($request->hasFile('attachment')){
                $filepath = Storage::putfile('employee_ledgers', $request->file('attachment'));
                $vLedgerItem->attachment = $filepath;
            }

            $vLedgerItem->save();


            # --------------------------
            #   Add Ledger
            # --------------------------

            # Save ledger
            $ledger = new Ledger;
            $ledger->type="dr";
            $ledger->source_id=$vLedgerItem->_id;
            $ledger->source_model=get_class($vLedgerItem);
            $ledger->date=$date;
            $ledger->month = $month; // For Filteration Purpose
            $ledger->tag="employee_ledger";
            $ledger->is_cash=false;
            $ledger->amount=$amount;
            $ledger->props=[
                'by'=>Auth::user()->id
            ];
            $ledger->save();

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'employee_ledger';
            $relation->is_real = true;
            $relation->save();

        }
        else{

            # --------------------------
            #   Add Statement Ledger Item
            # --------------------------

            $vLedger = StatementLedger::ofNamespace($request->namespace, $request->resource_id)->get()->first();


            if(!isset($vLedger)){
                $vLedger = new StatementLedger;
                $vLedger->linked_to =$request->namespace;
                $vLedger->linked_id =(int)$request->resource_id;
                $vLedger->save();
            }

            $vItemObj = (object)[
                'title' => $addon->setting->title . " Charge",
                'description' => $description,
                'type' => "dr",
                'tag' => strtolower( Str::slug( preg_replace('/\s+/', '', $addon->setting->title) ) ) . '_addon',
                'date' => $date,
                'month' => $month,
                'amount' => $amount
            ];


            #need to check if image added
            $filepath = null;
            if($request->hasFile('attachment')){
                $filepath = Storage::putfile('vehicle_ledgers', $request->file('attachment'));
                $vItemObj->attachment = $filepath;
            }

            $vLedgerItem =  $vLedger->addItem($vItemObj);


            # --------------------------
            #   Add Ledger
            # --------------------------

            # Save ledger
            $ledger = new Ledger;
            $ledger->type="dr";
            $ledger->source_id=$vLedgerItem->_id;
            $ledger->source_model=get_class($vLedgerItem);
            $ledger->date=$date;
            $ledger->month = $month; // For Filteration Purpose
            $ledger->tag="statementledger_transaction";
            $ledger->is_cash=false;
            $ledger->amount=$amount;
            $ledger->props=[
                'by'=>Auth::user()->id
            ];
            $ledger->save();

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'statementledger_transaction';
            $relation->is_real = true;
            $relation->save();

        }




        # ----------------------
        #   Add Addon Deduction
        # ----------------------

        $charge = new AddonDeduction;
        $charge->addon_id = $addon->id;
        $charge->date = $date;
        $charge->month = $month;
        $charge->description = $description;
        $charge->amount = $amount;

        # Need to check if image added
        if(isset($filepath)){
            $charge->attachment = $filepath;
        }

        $charge->save();

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $charge->id;
        $relation->source_model = get_class($charge);
        $relation->tag = 'addon_charge';
        $relation->is_real = false;
        $relation->save();


        return response()->json([
            'status' => 1,
            'message' => 'Amount Charged Successfully'
        ]);

    }


    /**
     * View Page
     *
    */
    public function viewSettings()
    {
        return view('Tenant.addons.setting.view');
    }

    /**
     * Create Page of Setting
     *
    */
    public function showSettingForm($config=null)
    {
        $addon_settings = AddonsSetting::all();


        return view('Tenant.addons.setting.create', compact('config', 'addon_settings'));
    }

    /**
     * POST request of creating the Setting
     *
    */
    public function create_setting(Request $request)
    {
        $request->validate([
            'source' => 'required|max:255',
            'title' => 'required|unique:addon_settings|max:255',
            'types' => 'required|array|max:255',
        ]);


        $types = [];
        foreach ($request->get('types', []) as $type) {
            $types[] = [
                'title' => $type['title'],
                'display_title' => $type['display_title'],
                'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                'charge' => isset($type['charge']) ? true : false,
            ];
        }

        $categories = [];
        foreach ($request->get('categories', []) as $category) {
            $categories[] = [
                'handle' => Str::slug($category['title'], '-'),
                'title' => $category['title'],
                'field_type' => $category['field_type'],
                'field_values' => $category['field_type'] === "text" ? null : $category['field_values'],
                'required' => isset($category['required']) ? true : false,
            ];
        }

        $conditions = null;
        if($request->has('conditions')){
            $conditions = $request->get('conditions', []);
        }

        $addon_setting = new AddonsSetting;
        $addon_setting->title = $request->get('title');
        $addon_setting->source_type = $request->get('source');
        $addon_setting->source_required = $request->has('source_required') ? true : false;
        $addon_setting->amount = $request->get('amount');
        $addon_setting->types = count($types) > 0 ? $types : null;
        $addon_setting->categories = count($categories) > 0 ? $categories : null;
        $addon_setting->conditions = $conditions;

        $addon_setting->save();

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
        $addon_setting = AddonsSetting::find($id);

        $addon_setting->actions=[
            'status'=>1,
        ];

        # Call the load job function
        return $this->showSettingForm((object)[
            'addon_setting'=>$addon_setting,
            'action'=>'edit'
        ]);

    }

    public function edit_setting(Request $request)
    {
        $addon_setting = AddonsSetting::findOrFail($request->addon_setting_id);

        $request->validate([
            'source' => 'required|max:255',
            'title' => [
                'required',
                'max:255',
                Rule::unique('addon_settings')->ignore($addon_setting->_id, '_id'),
            ],
            'types' => 'required|array|max:255',
        ]);


        $types = [];
        foreach ($request->get('types', []) as $type) {
            $types[] = [
                'title' => $type['title'],
                'display_title' => $type['display_title'],
                'amount' => isset($type['amount']) ? (float) $type['amount'] : null,
                'charge' => isset($type['charge']) ? true : false,
            ];
        }

        $categories = [];
        foreach ($request->get('categories', []) as $category) {
            $categories[] = [
                'handle' => Str::slug($category['title'], '-'),
                'title' => $category['title'],
                'field_type' => $category['field_type'],
                'field_values' => $category['field_type'] === "text" ? null : $category['field_values'],
                'required' => isset($category['required']) ? true : false,
            ];
        }

        $conditions = null;
        if($request->has('conditions')){
            $conditions = $request->get('conditions', []);
        }

        $addon_setting->title = $request->get('title');
        $addon_setting->source_type = $request->get('source');
        $addon_setting->source_required = $request->has('source_required') ? true : false;
        $addon_setting->amount = $request->get('amount');
        $addon_setting->types = count($types) > 0 ? $types : null;
        $addon_setting->categories = count($categories) > 0 ? $categories : null;
        $addon_setting->conditions = $conditions;

        $addon_setting->update();

        return response()->json([
            'status' => 1
        ]);
    }


    public function showAddonExpenseForm(Request $request, $type)
    {

        $all_addons = Addon::with([
            'setting',
            'link' => function($query){
                $query->select('id', 'plate', 'chassis_number', 'vehicle_booking_id', 'name', 'booking_id');
            },
            'expenses' => function($query){
                $query->select('id', 'addon_id', 'type');
            },

        ])
        ->where('source_type', $type)
        ->whereNotNull('source_id')
        ->get();

        $addons = $all_addons->map(function($item) use ($type){
            $text = $item->setting->title;
            if($type === "driver"){
                $text .= " | " . $item->link->full_name;
            }
            elseif($type === "staff"){
                $text .= " | " . $item->link->name;
            }
            if($type === "vehicle"){
                if($item->source_model === VehicleBooking::class){
                    $text .= " | " . 'B#'.$item->link->id;
                }
                else{
                    if(isset($item->link->vehicle_booking)){
                        $text .= " | " . 'V#'.$item->link->vehicle_booking->id.' / '.$item->link->plate.' / '.$item->link->chassis_number;
                    }
                    else{
                        $text .= " | " . $item->link->plate.' / '.$item->link->chassis_number;
                    }
                }
            }
            return [
                'id' => $item->id,
                'text' => $text
            ];
        });


        return view('Tenant.addons.create_expense', compact('type', 'addons', 'all_addons'));
    }

    public function saveAddonExpense(Request $request) {
        # This request involve cash, we better validate accounts
        \App\Accounts\Handlers\AccountGateway::validateCookie();

        $validated = $request->validate([
            'amount' => ['required','gt:0'],
            'source_type' => ['required','in:driver,vehicle,staff'],
            'type' => ['required','max:255'],
            'addon' => ['required']
        ]);

        # Payload
        $month=Carbon::parse($request->month)->format('Y-m-d');
        $given_date=Carbon::parse($request->given_date)->format('Y-m-d');
        $type=$request->type;
        $display_title=isset($request->display_title) && $request->display_title !== '' ? $request->display_title : $request->title;
        $description=$request->description;

        $charge_amount = null;
        if(isset($request->from_source)){
            $charge_amount = floatval($request->charge_amount);
        }
        $amount=(float)$request->amount;
        $attachment=null;
        $addon_id = intval($request->addon);

        $prefix = null;

        $addon = Addon::with([
            'setting' => function($query){
                $query->select('id', 'title', 'conditions');
            },
            'link' => function($query){
                $query->select('id', 'plate', 'chassis_number', 'vehicle_booking_id', 'name', 'booking_id');
            },
        ])
        ->findOrFail($addon_id);

        if($addon->source_type === "driver"){
            // Since driver can is nullable
            if(isset($addon->source_id)){

                $prefix = [
                    'text' => $addon->link->full_name . (isset($charge_amount) && $charge_amount > 0 ? " (Charged)" : ""),
                    'url' => route('tenant.admin.drivers.viewDetails', $addon->link->id)
                ];

            }
        }
        else if($addon->source_type === "staff"){
            // Since driver can is nullable
            if(isset($addon->source_id)){

                $prefix = [
                    'text' => $addon->link->name . (isset($charge_amount) && $charge_amount > 0 ? " (Charged)" : ""),
                    'url' => route('tenant.admin.employee.ledger.view').'?m='.$month.'&e='.$addon->link->id
                ];

            }
        }
        else{
            if($addon->source_model === VehicleBooking::class){
                $prefix = [
                    'text' => 'B#'.$addon->link->id . (isset($charge_amount) && $charge_amount > 0 ? " (Charged)" : "")
                ];
            }
            else{
                if(isset($addon->link->vehicle_booking_id)){
                    $prefix = [
                        'text' => 'V#'.$addon->link->vehicle_booking_id.' / '.$addon->link->plate . ' / ' . $addon->link->chassis_number . (isset($charge_amount) && $charge_amount > 0 ? " (Charged)" : "")
                    ];
                }
                else{
                    $prefix = [
                        'text' => $addon->link->plate . ' / ' . $addon->link->chassis_number . (isset($charge_amount) && $charge_amount > 0 ? " (Charged)" : "")
                    ];
                }
            }

        }

        #need to check if image added
        if($request->hasFile('attachment')){
            $filepath = Storage::putfile('addon_expenses', $request->file('attachment'));
            $attachment = $filepath;
        }

        $user = Auth::user();



        # Create expense
        $expense = new AddonExpense;
        $expense->addon_id = $addon_id;
        $expense->month = $month;
        $expense->given_date = $given_date;
        $expense->type = $type;
        $expense->charge_amount = $charge_amount;
        $expense->description = $description;
        $expense->amount = $amount;
        $expense->attachment = $attachment;
        $expense->save();
        // When expense Created Add Status as In Progress and Set Current Stage as Expense Type
        $addon->status = 'inprogress';
        $addon->current_stage = $display_title;
        if($addon->source_type === "driver"){
            $addon->link->status = $display_title;
            $addon->link->save();
        }
        $addon->payment_status = 'pending';
        $addon->save();
        $selected_account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();

        # -------------------
        #  Apply Conditions
        # -------------------
        if(isset($addon->setting->conditions) && count($addon->setting->conditions) > 0){
            $conditions = collect($addon->setting->conditions);
            $setting_ids = $conditions->pluck('setting_id');

            # Fetch all addons of source matching conditions setting_id
            $source_addons = Addon::whereHas('setting', function($query) use ($setting_ids){
                $query->whereIn('_id', $setting_ids);
            })
            ->where('source_id', $addon->source_id)
            ->where('source_model', $addon->source_model)
            ->with([
                'setting' => function($query){
                    $query->select('_id', 'title');
                }
            ])
            ->get();

            # Apply each condition
            if(count($source_addons) > 0){
                foreach ($conditions as $condition) {

                    if(strtolower($type) === strtolower($condition['type'])){
                        // change the status of source addons of setting_id to status
                        $source_addon = $source_addons->where('setting._id', $condition['setting_id'])->first();
                        if(isset($source_addon)){
                            $source_addon->status = $condition['status'];
                            $source_addon->update();
                        }
                    }
                }
            }
        }


        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$expense->_id;
        $ledger->source_model=get_class($expense);
        $ledger->date=$given_date;
        $ledger->month = $month; // For Filteration Purpose
        $ledger->tag="addon_expense";
        $ledger->is_cash=true;
        $ledger->amount=$amount;
        $ledger->props=[
            'by'=>$user->id,
            'account'=>[
                'id'=>$selected_account->_id,
                'title'=>$selected_account->title
            ],
            'prefix' => $prefix
        ];
        $ledger->save();


        #create account transaction
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
            'type'=>'dr',
            'title'=>$addon->setting->title . ' Addon Expense',
            'description'=>$type.' | '.$description.' | '.Carbon::parse($month)->format('M Y'),
            'tag'=>'addon_expense',
            'date' => $given_date,
            'amount'=>$amount,
            'links'=>[
                [
                    'modal'=>get_class(new AddonExpense),
                    'id'=>$expense->id,
                    'tag'=>'addon_expense'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ]
            ]
        ]);


        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $expense->id;
        $relation->source_model = get_class($expense);
        $relation->tag = 'addon_expense';
        $relation->is_real = true;
        $relation->save();

        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();


        # Modify ledger model so it can load on ledger page
        $ledger->source = $expense;
        $ledger->user = $user;
        $ledger->generated_source = $ledger->source;
        $ledger->description = \App\Http\Controllers\Tenant\LedgerController::generate_description($ledger);
        $ledger->cr=0;
        $ledger->dr=$ledger->amount;
        $ledger->account = $selected_account->title;
        $ledger->paid_by = $user->name;
        $ledger->actions = [
            'status'=>1,
        ];
        $ledger->date=Carbon::parse($ledger->date)->format('d F, Y');


        // Converting Ledger To Array And Chnage Time To Required Format As We Cannot Change Format Of Database Datetime Object
        $ledger_obj = $ledger->toArray();
        $ledger_obj['date'] = Carbon::parse($ledger->date)->format('d F, Y');

        # We will return ledger because expense will be added from ledger page
        return response()->json($ledger_obj);
    }

    public function showBreakDownView(Request $request, int $addon_id){
        $view = $request->get('view', 'default');

        $addon = Addon::with([
            'setting' => function($query){
                $query->select('addon_id','title', 'types');
            },
            'expenses' => function($query){
                $query->select('addon_id','given_date','amount', 'charge_amount', 'type', 'description','date');
            },
            'deductions' => function($query){
                $query->select('addon_id', 'description', 'date', 'amount');
            },
            'link' => function($query){
                $query->select('id', 'plate', 'chassis_number', 'vehicle_booking_id', 'name', 'booking_id');
            },
        ])
        ->where('id',$addon_id)->first();

        if(!isset($addon->source_id)){
            $text =  'Available';
        }
        else if($addon->source_type === "driver"){
            $text = $addon->link->full_name;
        }
        else if($addon->source_type === "staff"){
            $text = $addon->link->name;
        }
        else{
            if($addon->source_model === VehicleBooking::class){
                $text = 'B#'.$addon->link->id;
            }
            else{
                if(isset($addon->link->vehicle_booking_id)){
                    $text = 'V#'.$addon->link->vehicle_booking_id.' / '.$addon->link->plate.' / '.$addon->link->chassis_number;
                }
                else{
                    $text = $addon->link->plate.' / '.$addon->link->chassis_number;
                }
            }
        }
        $breakdown_title = "( ".$addon->setting->title. " - $text )";
        $breakdown = $addon->breakdown;
        $expenses = $breakdown->expenses;
        $pending_expenses_for_addon = collect([]);
        foreach($addon->setting->types as $type){
            $expense = $expenses->where('type', $type['title'])->first();
            if(!isset($expense)){
                $pending_expenses_for_addon->push($type);
            }
        }

        $statement_breakdown = [];
        if($view === 'statement' || $view === 'inline_statement'){


            # Append All chargeable expenses
            foreach ($addon->expenses->whereNotNull('charge_amount')->where('charge_amount', '>', 0) as $item) {
                $statement_breakdown[] = (object)[
                    'title' => $item->type,
                    'date' => $item->given_date,
                    'description' => $item->description,
                    'type' => 'cr',
                    'amount' => $item->charge_amount
                ];
            }

            # Append All charged amounts
            foreach ($addon->deductions as $item) {
                $statement_breakdown[] = (object)[
                    'title' => "Charged" . (isset($item->description) && $item->description !== '' ? " | ".$item->description : ''),
                    'date' => $item->date,
                    'description' => "",
                    'type' => 'dr',
                    'amount' => $item->amount
                ];
            }


            $statement_breakdown = collect($statement_breakdown)->sortBy('date');

            # Append Addon->Price as first row
            $statement_breakdown->prepend((object)[
                'title' => "<b>BASE:</b> ".$addon->setting->title,
                'date' => $addon->date,
                'description' => $addon->readable_details ?? "",
                'type' => 'cr',
                'amount' => $addon->price
            ]);

        }

        return view('Tenant.addons.breakdown',compact('breakdown','addon','breakdown_title', 'view', 'statement_breakdown', 'pending_expenses_for_addon'));
    }

    public function showEditForm(int $addon_id) {
        $addon = Addon::findOrFail($addon_id);
        $sources = collect([]);

        if(!isset($addon->source_id)){

            if($addon->source_type === 'driver'){
                $sources = Driver::select('id', 'name')
                ->get()
                ->map(function($item){
                    return [
                        'id' => $item->id,
                        'text' => $item->full_name
                    ];
                });
            }
            else if($addon->source_type === 'staff'){
                $sources = User::select('_id', 'name')
                ->employees()
                ->get()
                ->map(function($item){
                    return [
                        'id' => $item->id,
                        'text' => $item->name
                    ];
                });
            }
            else{
                $sources = Vehicle::select('id', 'plate', 'chassis_number', 'vehicle_booking_id')
                ->with([
                    'vehicle_booking' => function($query){
                        $query->select('id');
                    }
                ])
                ->get()
                ->map(function($item){
                    $text = $item->plate.' / '.$item->chassis_number;
                    if(isset($item->vehicle_booking)){
                        $text = 'V#'.$item->vehicle_booking->id.' / '.$item->plate.' / '.$item->chassis_number;
                    }

                    return [
                        'id' => $item->id,
                        'type' => 'vehicle',
                        'text' => $text
                    ];
                });

                $bookings = VehicleBooking::select('id')
                ->whereDoesntHave('vehicle')
                ->get()
                ->map(function($item){
                    $text = 'B#'.$item->id;
                    return [
                        'id' => $item->id,
                        'type' => 'booking',
                        'text' => $text
                    ];
                });

                $sources = $sources->merge($bookings);
            }
        }
        return view('Tenant.addons.edit', compact('addon','sources'));
    }

    public function edit(Request $request, int $addon_id){
        $request->validate([
            'amount' => ['required','numeric']
        ]);
        $addon = Addon::findOrFail($addon_id);
        if(isset($request->source_id)){
            $addon->source_id = $request->source_id;
            if($addon->source_type !== "staff"){
                $addon->source_id = (int) $request->source_id;
            }

            # If addon is vehicle type, we need to check if its booking
            # then change source to booking
            if($addon->source_type === 'vehicle'){
                $vehicle_type = $request->get('vehicle_type', null);

                if(isset($vehicle_type) && $vehicle_type === 'booking'){
                    # Update source model to booking
                    # it was by default vehicle, since no source was attached
                    # but now we are sure that its booking and "source_id" will be booking id
                    $addon->source_model = VehicleBooking::class;
                }
            }
        }
        $addon->price = intval($request->amount);
        $addon->save();
        return response()->json($addon);
    }

    public function changeStatusAction(int $addon_id){
        $addon = Addon::findOrFail($addon_id);

        if($addon->source_type === "driver"){

            # -----------------
            # Validate Driver
            # -----------------
            $addonType = '';
            $setting = $addon->setting;
            if(isset($setting)){

                if(strtolower($setting->title) === 'visa') $addonType = 'visa';
                else if(str_contains(strtolower($setting->title), "license")) $addonType = 'license';
                else if(str_contains(strtolower($setting->title), "rta")) $addonType = 'rta';

                $missing_fields = collect($addon->link->missing_fields)
                ->where('addon', $addonType)
                ->values();

                if(count($missing_fields) > 0){
                    // Show error, to completed driver before

                    $errors = [];
                    foreach ($missing_fields as $missing_field) {
                        $errors[] = $missing_field['text'] . ' is required';
                    }

                    throw ValidationException::withMessages($errors);
                }

            }




            $addon->link->status = 'completed';
            $addon->link->save();
        }
        $addon->status = 'completed';
        $addon->save();
        return response()->json($addon);
    }

    public function markAsPaidAction(int $addon_id){
        $addon = Addon::findOrFail($addon_id);
        $addon->payment_status = 'paid';
        $addon->save();
        return response()->json($addon);
    }
    // BreakDown Action Routes Will Go There
    public function breakdown_edit_action(Request $request, $id){
        $expense = AddonExpense::findOrFail($id);
        $request->validate([
            'amount' => 'gt:0|numeric|required'
        ]);

        // Fetch Associated Ledger Entry And Edit Amount Because Only Ledger Entry Is Associated With Expense On Creation
        $ledger_controller_instance = new LedgerController;
        Ledger::where('source_id', $expense->_id)
        ->where('source_model', get_class($expense))
        ->get()
        ->each(function($item) use ($request, $ledger_controller_instance){
            $request->merge(['ledger_id' => $item->id]);
            $request->merge(['effect_all' => true]);
            $ledger_controller_instance->edit($request);
        });

        return response()->json(['status' => true]);
    }

    public function breakdown_delete_action($id){
        $expense = AddonExpense::findOrFail($id);

        // Fetch Associated Ledger Entry And Delete Because Only Ledger Entry Is Associated With Expense On Creation
        $ledger_controller_instance = new LedgerController;
        Ledger::where('source_id', $expense->_id)
        ->where('source_model', get_class($expense))
        ->get()
        ->each(function($item) use ($ledger_controller_instance){
            $ledger_controller_instance->delete($item->id);
        });

        return response()->json(['status' => true]);
    }
    public function breakdown_charge_action(Request $request, $id){
        $expense = AddonExpense::findOrFail($id);
        $request->validate([
            'amount' => 'gt:0|numeric|required'
        ]);
        $amount = floatval($request->get('amount',$expense->amount));
        $expense->charge_amount = $amount;
        return response()->json(['status' => $expense->update()]);
    }
    public function breakdown_remove_charge_action($id){
        $expense = AddonExpense::findOrFail($id);
        $expense->charge_amount = null;
        return response()->json(['status' => $expense->update()]);
    }



}
