<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Auth;

use App\Models\Tenant\Client;
use App\Accounts\Handlers\AccountGateway;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\Investor;
use App\Models\Tenant\VehicleHistory;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\VehicleLedgerItem;
use App\Models\Tenant\VehicleType;
use Illuminate\Support\Facades\Redirect;

class BookingController extends Controller
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
     * View Page of booking
     *
    */
    public function ViewBookings($active_status)
    {
        if(in_array($active_status, ['active', 'inactive'])) return view('Tenant.booking.open.view', compact('active_status'));
        return view('403');
    }

    /**
     * View Page of booking - CLosed
     *
    */
    public function ViewClosedBookings()
    {
        return view('Tenant.booking.closed.view');
    }


    /**
     * Create view Page of booking addition
     *
    */
    public function showBookingForm($config=null)
    {

        $investors = Investor::select('id', 'refid', 'name')->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => $item->refid.' '.$item->name,
                'selected' => (int)old('investor_id') === (int)$item->id
            ];
        });
        $vehicle_types = VehicleType::all()
        ->map(function($item){
            return [
                'id' => $item->_id,
                'text' => $item->make.' '.$item->model.' '.$item->cc,
                'selected' => old('vehicletype_id') === $item->_id
            ];
        });

        return view('Tenant.booking.create', compact('config', 'investors', 'vehicle_types'));
    }

    /**
     * POST request of creating the booking
     *
    */
    public function create(Request $request)
    {
        // Before validation, cast the integer values, otherwise validation may not work as expected
        $request->merge(['investor_id' => (int)$request->investor_id]);

        $request->validate([
            'investor_id' => 'required|integer|exists:investors,id',
            'vehicletype_id' => 'required|exists:vehicle_types,_id',
            'amount' => 'required|numeric',
            'delivery_date' => 'required|date',
            'account_id' => 'required|max:255'
        ]);

        $selected_account = AccountGateway::getAccount($request->account_id);
        $request->merge(['account_title' => $selected_account->title]);

        # Check if "preview" key found in request, we need to show preview popup before save
        if($request->has('preview')){
            $request->merge(['preview' => 1]);
            return back()->withInput();
        }

        $amount= $request->has('amount') ? (float)$request->amount : 0;
        $date=Carbon::now()->format('Y-m-d');
        $month=Carbon::now()->startOfMonth()->format('Y-m-d');

        $booking = new VehicleBooking;
        $booking->investor_id = (int)$request->investor_id;
        $booking->vehicle_type_id = $request->vehicletype_id;
        $booking->initial_amount = $amount;
        $booking->date = $date;
        $booking->notes = $request->notes;
        $booking->status = "open";
        $booking->activation_status = "active";

        $booking->save();


        $vLedger = new VehicleLedger;
        $vLedger->vehicle_booking_id = $booking->id;
        $vLedger->vehicle_id = null;
        $vLedger->save();


        if($amount > 0){

            $vLedgerItem = $vLedger->addItem((object)[
                'title' => "Initial deposit",
                'type' => 'cr',
                'tag' => 'vehicle_booking_initial_amount',
                'date' => Carbon::now()->format('Y-m-d'),
                'month' => $month,
                'amount' => $amount
            ]);

            # Save ledger
            $ledger = new Ledger;
            $ledger->type="cr";
            $ledger->source_id=$booking->id;
            $ledger->source_model=get_class($booking);
            $ledger->date=Carbon::now()->format('Y-m-d');
            $ledger->month = $month;
            $ledger->is_cash=true;
            $ledger->tag="vehicle_booking";
            $ledger->amount=$amount;
            $ledger->props=[
                'by'=>Auth::user()->id,
                'account'=>[
                    'id'=>$selected_account->_id,
                    'title'=>$selected_account->title
                ]
            ];
            $ledger->save();

            /*
            |--------------------------------------------------------------------------
            | Add account transaction
            |--------------------------------------------------------------------------
            */

            $transaction = AccountGateway::add_transaction([
                'account_id'=>$selected_account->_id,
                'type'=>'cr',
                'title'=>'Vehicle Booking Initial Amount',
                'description'=>'Investor: '.$booking->investor->refid.' '.$booking->investor->name,
                'tag'=>'statementledger_transaction',
                'amount'=>$amount,
                'additional_details' => [
                    "vehicle_booking_id" => $booking->id,
                    "vehicle_id" => null,
                    "type" => "booking",
                    "attachment" => null
                ],
                'links'=>[
                    [
                        'modal'=>get_class(new VehicleBooking),
                        'id'=>$booking->id,
                        'tag'=>'vehicle_booking'
                    ],
                    [
                        'modal'=>get_class(new Ledger),
                        'id'=>$ledger->id,
                        'tag'=>'ledger'
                    ]
                ]
            ]);

            #add relations

            # --booking
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $booking->id;
            $relation->source_model = get_class($booking);
            $relation->tag = 'vehicle_booking';
            $relation->is_real = true;
            $relation->save();

            # --transaction
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $transaction->id;
            $relation->source_model = get_class($transaction);
            $relation->tag = 'transaction';
            $relation->is_real = false;
            $relation->save();

            # --VehicleLedgerItem
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'statementledger_transaction';
            $relation->is_real = false;
            $relation->save();
        }


        return redirect()->route('tenant.admin.bookings.single.view', $booking->id)->with('message', "Booking #$booking->id is created successfully!");
    }

    /**
     * Details page of booking
     *
    */
    public function showDetails($id)
    {
        $booking = VehicleBooking::with([
            'investor' => function($query){
                $query->select('id', 'name', 'refid');
            },
            'vehicle_type' => function($query){
                $query->select('make', 'model', 'cc');
            },
            'vehicle',
            'reserve_vehicle',
            'account_relation.transaction.account'
        ])
        ->findOrFail((int)$id);

        return view('Tenant.booking.detail', compact('booking'));
    }

    /**
     * GET request of editing the page
     *
    */
    public function showEditForm($id)
    {
        $booking = VehicleBooking::with('account_relation.transaction.account')
        ->findOrFail((int)$id);
        if($booking->status === "closed") abort(422, "Cannot edit vehicle");
        $booking->actions=[
            'status'=>1,
        ];
        $booking->account_id=null;

        if(isset($booking->account_relation)) $booking->account_id=$booking->account_relation->transaction->account->id;

        # Call the load function
        return $this->showBookingForm((object)[
            'booking'=>$booking,
            'action'=>'edit'
        ]);

    }

    public function edit(Request $request)
    {

        $booking = VehicleBooking::with([
            'account_relation.transaction.account'
        ])
        ->findOrFail((int)$request->booking_id);

        // Before validation, cast the integer values, otherwise validation may not work as expected
        $request->merge(['investor_id' => (int)$request->investor_id]);

        $request->validate([
            'investor_id' => 'required|integer|exists:investors,id',
            'vehicletype_id' => 'required|exists:vehicle_types,_id',
            'amount' => 'required|numeric',
            'delivery_date' => 'required|date',
            'account_id' => 'required|max:255'
        ]);

        $selected_account = AccountGateway::getAccount($request->account_id);
        $request->merge(['account_title' => $selected_account->title]);

        # Check if "preview" key found in request, we need to show preview popup before save
        if($request->has('preview')){
            $request->merge(['preview' => 1]);
            return back()->withInput();
        }

        $amount= $request->has('amount') ? (float)$request->amount : 0;
        $date=Carbon::parse($request->delivery_date)->format('Y-m-d');
        $month=Carbon::now()->startOfMonth()->format('Y-m-d');

        $booking->investor_id = (int)$request->investor_id;
        $booking->vehicle_type_id = $request->vehicletype_id;
        $booking->initial_amount = $amount;
        $booking->date = $date;
        $booking->notes = $request->notes;

        $booking->save();


        $vLedger = new VehicleLedger;
        $exists = VehicleLedger::where('vehicle_booking_id', $booking->id)->whereNull('vehicle_id')->get()->first();
        if(isset($exists)) $vLedger = $exists;
        $vLedger->vehicle_booking_id = $booking->id;
        $vLedger->vehicle_id = null;
        $vLedger->save();



        if($amount > 0){

            $vLedgerItem = $vLedger->items()->where('tag', 'vehicle_booking_initial_amount')->get()->first();

            if(isset($vLedgerItem)){
                $vLedger->updateItem($vLedgerItem->id, (object)[
                    'title' => "Initial deposit",
                    'type' => 'cr',
                    'tag' => 'vehicle_booking_initial_amount',
                    'date' => $date,
                    'amount' => $amount
                ]);
            }
            else{
                $vLedgerItem = $vLedger->addItem((object)[
                    'title' => "Initial deposit",
                    'type' => 'cr',
                    'tag' => 'vehicle_booking_initial_amount',
                    'date' => $date,
                    'month' => $month,
                    'amount' => $amount
                ]);
            }

            # Save ledger
            $ledger = new Ledger;
            $exists = Ledger::where('source_id', $booking->id)
            ->where('source_model', get_class($booking))
            ->where('tag', 'vehicle_booking')
            ->where('is_cash', true)
            ->get()
            ->first();
            if(isset($exists)) $ledger = $exists;

            $ledger->type="cr";
            $ledger->source_id=$booking->id;
            $ledger->source_model=get_class($booking);
            $ledger->date=$date;
            if(!isset($exists)) $ledger->month = $month;
            $ledger->is_cash=true;
            $ledger->tag="vehicle_booking";
            $ledger->amount=$amount;
            $ledger->props=[
                'by'=>Auth::user()->id,
                'account'=>[
                    'id'=>$selected_account->_id,
                    'title'=>$selected_account->title
                ]
            ];
            $ledger->save();

            /*
            |--------------------------------------------------------------------------
            | Add account transaction
            |--------------------------------------------------------------------------
            */

            if(isset($exists)){
                // means ledger found, edit the transaction

                $transaction_id = $booking->account_relation->transaction->id;



                AccountGateway::edit_transaction([
                    'transaction_id' => $transaction_id,
                    'account_id'=>$selected_account->_id,
                    'type'=>'cr',
                    'title'=>'Vehicle Booking Initial Amount',
                    'description'=>'Investor: '.$booking->investor->refid.' '.$booking->investor->name,
                    'tag'=>'statementledger_transaction',
                    'amount'=>$amount,
                    'additional_details' => [
                        "vehicle_booking_id" => $booking->id,
                        "vehicle_id" => null,
                        "type" => "booking",
                        "attachment" => null
                    ],
                    'links'=>[
                        [
                            'modal'=>get_class(new VehicleBooking),
                            'id'=>$booking->id,
                            'tag'=>'vehicle_booking'
                        ],
                        [
                            'modal'=>get_class(new Ledger),
                            'id'=>$ledger->id,
                            'tag'=>'ledger'
                        ]
                    ]
                ]);
            }
            else{
                // no ledger was found? create transaction

                /*
                |--------------------------------------------------------------------------
                | Add account transaction
                |--------------------------------------------------------------------------
                */

                $transaction = AccountGateway::add_transaction([
                    'account_id'=>$selected_account->_id,
                    'type'=>'cr',
                    'title'=>'Vehicle Booking Initial Amount',
                    'description'=>'Investor: '.$booking->investor->refid.' '.$booking->investor->name,
                    'tag'=>'statementledger_transaction',
                    'amount'=>$amount,
                    'additional_details' => [
                        "vehicle_booking_id" => $booking->id,
                        "vehicle_id" => null,
                        "type" => "booking",
                        "attachment" => null
                    ],
                    'links'=>[
                        [
                            'modal'=>get_class(new VehicleBooking),
                            'id'=>$booking->id,
                            'tag'=>'vehicle_booking'
                        ],
                        [
                            'modal'=>get_class(new Ledger),
                            'id'=>$ledger->id,
                            'tag'=>'ledger'
                        ]
                    ]
                ]);

                #add relations

                # --booking
                $relation = new Table_relation;
                $relation->ledger_id = $ledger->id;
                $relation->source_id = $booking->id;
                $relation->source_model = get_class($booking);
                $relation->tag = 'vehicle_booking';
                $relation->is_real = true;
                $relation->save();

                # --transaction
                $relation = new Table_relation;
                $relation->ledger_id = $ledger->id;
                $relation->source_id = $transaction->id;
                $relation->source_model = get_class($transaction);
                $relation->tag = 'transaction';
                $relation->is_real = false;
                $relation->save();

                # --VehicleLedgerItem
                $relation = new Table_relation;
                $relation->ledger_id = $ledger->id;
                $relation->source_id = $vLedgerItem->_id;
                $relation->source_model = get_class($vLedgerItem);
                $relation->tag = 'statementledger_transaction';
                $relation->is_real = false;
                $relation->save();
            }

        }
        else{
            # Delete amounts in found

            $ledger = Ledger::where('source_id', $booking->id)
            ->where('source_model', get_class($booking))
            ->where('tag', 'vehicle_booking')
            ->where('is_cash', true)
            ->get()
            ->first();
            if(isset($ledger)){
                $ledger_relations = $ledger->relations;

                foreach ($ledger_relations as $relation) {
                    # Delete source
                    $source = $relation->source;
                    if($relation->tag !== "vehicle_booking") {

                        if($relation->tag === "transaction"){
                            # delete relations too
                            $source->links()->delete();
                        }
                        $source->delete();


                    }

                    # Delete relation too
                    $relation->delete();


                }
                # Delete Ledger
                $ledger->delete();
            }
        }

        return redirect()->route('tenant.admin.bookings.single.view', $booking->id)->with('message', "Booking #$booking->id is updated successfully!");


    }


    /**
     * Create view Page of booking addition
     *
    */
    public function showAddVehicleForm($config=null)
    {

        $vehicles = Vehicle::whereNull('vehicle_booking_id')->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => (isset($item->plate) ? $item->plate . ' / ' : '') . $item->chassis_number . ' / ' . ucfirst($item->color) . ' / ' . $item->model,
                'selected' => old('vehicle_id') === $item->id
            ];
        });

        $reserve_only = false;

        return view('Tenant.booking.vehicle.create', compact('config', 'vehicles', 'reserve_only'));
    }

    /**
     * POST request of adding vehicle details to booking
     *
    */
    public function add_vehicle(Request $request)
    {
        // Before validation, cast the integer values, otherwise validation may not work as expected
        $vehicle_id = (int)$request->vehicle_id;
        $booking_id = (int)$request->booking_id;
        $request->merge(['vehicle_id' => $vehicle_id]);
        $request->merge(['booking_id' => $booking_id]);

        $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'booking_id' =>  'required|integer|exists:vehicle_bookings,id',
            'assign_date' => 'required'
        ]);

        $vehicle = Vehicle::findOrFail($vehicle_id);
        $booking = VehicleBooking::findOrFail($booking_id);

        // Assign vehicle to booking
        $vehicle->vehicle_booking_id = $booking_id;
        $vehicle->reserve_vehicle_booking_id = null; // remove reservation
        $vehicle->update();

        // Add Temp Vehicle Booking History Item
        $booking_history = new VehicleHistory;
        $booking_history->booking_id = $booking_id;
        $booking_history->vehicle_id = $vehicle_id;
        $booking_history->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $booking_history->unassign_date = null; # Active
        $booking_history->save();

        // Merge accounts
        //  1) Get vehicle_ledgers against this vehicle
        //  2) add booking_id to all entries
        //  3) from now, all entries should be against booking_id, having vehicle_id=null

        $vehicle->vehicle_ledgers()->update([
            'vehicle_booking_id' => $booking_id
        ]);

        // Merge drivers
        //  1) Get drivers against this vehicle
        //  2) add booking_id to all entries and remove vehicle_id


        $vehicle->drivers()->update([
            'booking_id' => $booking_id,
            'vehicle_id' => null
        ]);


        // Close booking
        $booking->status = "closed";
        $booking->update();

        return response()->json($vehicle);

    }


    /**
     * Create view Page of booking addition
     *
    */
    public function showReserveVehicleForm($config=null)
    {

        $vehicles = Vehicle::whereNull('reserve_vehicle_booking_id')
        ->whereNull('vehicle_booking_id')
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => (isset($item->plate) ? $item->plate . ' / ' : '') . $item->chassis_number . ' / ' . ucfirst($item->color) . ' / ' . $item->model,
                'selected' => old('vehicle_id') === $item->id
            ];
        });

        $reserve_only = true;

        return view('Tenant.booking.vehicle.create', compact('config', 'vehicles', 'reserve_only'));
    }

    /**
     * POST request of adding vehicle details to booking
     *
    */
    public function reserve_vehicle(Request $request)
    {
        // Before validation, cast the integer values, otherwise validation may not work as expected
        $vehicle_id = (int)$request->vehicle_id;
        $booking_id = (int)$request->booking_id;
        $request->merge(['vehicle_id' => $vehicle_id]);
        $request->merge(['booking_id' => $booking_id]);

        $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'booking_id' =>  'required|integer|exists:vehicle_bookings,id',
        ]);

        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Assign vehicle to booking
        $vehicle->reserve_vehicle_booking_id = $booking_id;
        $vehicle->update();

        return response()->json($vehicle);

    }

    /**
     * DELETE request of Cancel Reservation of vehicle
     *
    */
    public function cancel_reservation_vehicle($id){

        $vehicle = Vehicle::findOrFail((int)$id);

        // Cancel reservation
        $vehicle->reserve_vehicle_booking_id = null;
        $vehicle->update();

        return response()->json([
            "status" => 1
        ]);
    }

    public function changeTmpVehicleView(Request $request, int $vehicle_id)
    {
        # Fetch TMP vehicle we want to change
        # : Fetch related statement entries too since we may moved them too
        $vehicle = Vehicle::with([
            'vehicle_booking' => function($query){
                $query->select('id');
            },
            'vehicle_booking.vehicle_ledgers' => function($query){
                $query->select('_id', 'vehicle_booking_id', 'vehicle_id');
            },
            'vehicle_booking.vehicle_ledgers.vehicle' => function($query){
                $query->select('id', 'plate');
            },
            'vehicle_booking.vehicle_ledgers.items',
            'vehicle_booking.vehicle_ledgers.items.driver' => function($query){
                $query->select('id', 'name');
            }
        ])
        ->findOrFail($vehicle_id);

        # Booking is required: so if no booking is attached to vehicle - its a red flag
        if(!isset($vehicle->vehicle_booking)){
            return abort(505, "Booking not found!");
        }

        $old_booking_id = $vehicle->vehicle_booking->id;

        # Merge all vehicle ledger items
        # We will use IDs to move them
        $vehicle_ledger_items = $vehicle
        ->vehicle_booking
        ->vehicle_ledgers

        # Add indicator for booking type and vehicle type entries
        ->map(function($vehicle_ledger){

            $rowType = isset($vehicle_ledger->vehicle_id) ? $vehicle_ledger->vehicle->plate : 'B#' . $vehicle_ledger->vehicle_booking_id;

            $vehicle_ledger->items = $vehicle_ledger->items->map(function($vehicle_ledger_item) use ($rowType){
                $vehicle_ledger_item->dt_rowtype = $rowType;
                return $vehicle_ledger_item;
            });

            return $vehicle_ledger;

        })
        ->pluck('items') # fetch items in vehicle ledgers
        ->flatten()
        ->values(); # Flatten them so they became single array

        # For moving: new booking id
        $new_bookings = VehicleBooking::with([
            'vehicle_type'
        ])
        ->where('status','open') // Only bookings
        ->get()
        ->map(function ($item){
            return [
                'id' => $item->id,
                'text' => 'B#'.$item->id . " - " . $item->vehicle_type->make . " - " . $item->vehicle_type->model,
                'selected' => false
            ];
        });

        # Title to render on frontend
        # Instead of sending all the data, we generate title here
        $title = "B#".$vehicle->vehicle_booking_id." / {$vehicle->plate} / {$vehicle->model} / {$vehicle->chassis_number}";

        return view('Tenant.booking.closed.change_tmp_vehicle', compact('old_booking_id', 'new_bookings', 'title', 'vehicle_id', 'vehicle_ledger_items'));
    }

    public function changeTmpVehicleAction(Request $request, int $vehicle_id)
    {
        // return $request->all();
        $request->validate([
            'new_booking_id' => ['required', 'numeric'],
            'old_booking_id' => ['required', 'numeric'],
            'assign_date' => ['required']
        ]);

        // Data Cleaning & Collecting
        $new_booking_id = (int) $request->new_booking_id;
        $old_booking_id = (int) $request->old_booking_id;

        # Load all data so if we have incorrect IDs it will sorted
        $old_booking = VehicleBooking::findOrFail($old_booking_id);
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $new_booking = VehicleBooking::findOrFail($new_booking_id);

        # -------------------------
        # -------------------------
        #   MOVE THE ENTRIES
        # -------------------------
        # -------------------------
        $selected_ledger_ids = $request->get('selected_ledger_ids', []);
        if(count($selected_ledger_ids) > 0){

            # ------------------------------------------
            # Find the ledger where to moved the enrties
            # ------------------------------------------


            # Make a array of to_be_moved entries and find their related vehicle_ledgers
            $source_vehicle_ledger_items_groups = VehicleLedgerItem::with([
                'vehicle_ledger'
            ])
            ->whereIn('_id', $selected_ledger_ids)
            ->get()

            # make groups of items so we can move entries to related ledger
            ->map(function($item) {

                $group = isset($item->vehicle_ledger->vehicle_id) ? 'vehicle' : 'booking';
                $source_id =  isset($item->vehicle_ledger->vehicle_id) ? $item->vehicle_ledger->vehicle_id : $item->vehicle_ledger->vehicle_booking_id;


                return [
                    'id' => $item->_id,
                    'source_id' => $source_id,
                    'group' => $group . '_' . $source_id,
                ];
            })
            ->groupBy('group');


            $vehicle_ledgers = VehicleLedger::where('vehicle_booking_id', $new_booking_id)->get()->toArray();

            # Final array of source vehicle_ledger_items ids and target vehicle_ledger id
            $final_vehicle_ledger_items = [];
            foreach ($source_vehicle_ledger_items_groups as $group => $source_vehicle_ledger_items) {

                foreach ($source_vehicle_ledger_items as $source_vehicle_ledger_item) {
                    $vehicle_ledger = null;

                    if(str_contains($group, 'vehicle')){
                        # items related to vehicle
                        # Find a vehicle_ledger against this vehicle
                        $vehicle_ledger = collect($vehicle_ledgers)
                        ->where('vehicle_id', $source_vehicle_ledger_item['source_id'])
                        ->first();

                        if(!isset($vehicle_ledger)){
                            $vehicle_ledger = new VehicleLedger;
                            $vehicle_ledger->vehicle_booking_id = $new_booking_id;
                            $vehicle_ledger->vehicle_id = $source_vehicle_ledger_item['source_id'];
                            $vehicle_ledger->save();

                            $vehicle_ledger = $vehicle_ledger->toArray();

                            # Push in target ledgers so next time it will pick it from there
                            $vehicle_ledgers[] = $vehicle_ledger;
                        }

                    }
                    else{
                        # items related to booking
                        # Find a vehicle_ledger where vehicle_id is null,
                        # So these entries kept linked to "Booking'
                        $vehicle_ledger = collect($vehicle_ledgers)
                        ->whereNull('vehicle_id')
                        ->first();

                        if(!isset($vehicle_ledger)){
                            $vehicle_ledger = new VehicleLedger;
                            $vehicle_ledger->vehicle_booking_id = $new_booking_id;
                            $vehicle_ledger->vehicle_id = null;
                            $vehicle_ledger->save();

                            $vehicle_ledger = $vehicle_ledger->toArray();

                            # Push in target ledgers so next time it will pick it from there
                            $vehicle_ledgers[] = $vehicle_ledger;
                        }
                    }


                    if(isset($vehicle_ledger)){

                        $final_vehicle_ledger_items[] = [
                            'id' => $source_vehicle_ledger_item['id'],
                            'statement_ledger_id' => $vehicle_ledger['_id']
                        ];
                    }


                }

            }

            # ---------------
            # Move the items
            # ---------------

            foreach ($final_vehicle_ledger_items as $final_vehicle_ledger_item) {

                $vItem = VehicleLedgerItem::find($final_vehicle_ledger_item['id']);
                if(isset($vItem)){
                    $vItem->statement_ledger_id = $final_vehicle_ledger_item['statement_ledger_id'];
                    $vItem->update();
                }
            }

        }

        # -------------------------
        # -------------------------
        #   MOVE THE VEHICLE
        # -------------------------
        # -------------------------

        # --------------------------------
        # Unlink vehicle from old booking
        # and link it to new booking
        # --------------------------------
        $old_booking->status = 'open';
        $old_booking->update();

        $vehicle->vehicle_booking_id = $new_booking_id;
        $vehicle->update();

        $new_booking->status = 'closed';
        $new_booking->update();

        # --------------------------------
        #      Add history record
        # --------------------------------

        # Inactive Old Vehicle History Item
        $active_history_item = $vehicle->vehicle_history()
        ->whereNull('unassign_date') // Active history
        ->first();
        if(isset($active_history_item)){
            # Make it inactive
            $active_history_item->unassign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
            $active_history_item->update();
        }

        # Create new Vehicle History Item
        $booking_history = new VehicleHistory;
        $booking_history->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $booking_history->unassign_date = null; # active
        $booking_history->vehicle_id = $vehicle->id;
        $booking_history->booking_id = $new_booking_id;
        $booking_history->save();

        return response()->json([
            'status' => 1,
            'message' => "TMP vehicle ($vehicle->plate) moved from #$old_booking->id to #$new_booking->id"
        ]);

    }

    public function deactivate($id){

        $vehicle = VehicleBooking::with('drivers')->findOrFail((int)$id);
        $closing_balance_checked = $vehicle->closing_balance !== 0;
        $drivers_checked = !$vehicle->drivers->isEmpty();
        if($closing_balance_checked) return response()->json(['status' => false], 403);
        if($drivers_checked) return response()->json(['status' => false], 403);
        $vehicle->activation_status = 'inactive';
        $vehicle->save();
        return response()->json([
            "status" => 1
        ]);
    }

    public function activate($id){

        $vehicle = VehicleBooking::with('drivers')->findOrFail((int)$id);
        $vehicle->activation_status = 'active';
        $vehicle->save();
        return response()->json([
            "status" => 1
        ]);
    }
    public function show_history(Request $request, $vehicle){
        $vehicle = Vehicle::with([
            'vehicle_history' => function($q){
                $q->orderByDesc('assign_date');
            },
            'vehicle_history.booking'
            ])->findOrFail((int)$vehicle);
        return view('Tenant.booking.closed.viewBookingHistory',compact('vehicle'));
    }
    public function edit_history(Request $request, $item_id){
        $request->validate([
            'save_all' => ['required', 'in:true,false'],
            'assign_date' => ['required']
        ]);
        $history_item = VehicleHistory::findOrFail($item_id);
        $save_unassign_date = $request->save_all === 'true';
        if($save_unassign_date && $request->has('unassign_date')){
            $history_item->unassign_date = Carbon::parse($request->unassign_date)->format('Y-m-d');
        }
        $history_item->assign_date = Carbon::parse($request->assign_date)->format('Y-m-d');
        $history_item->save();
        return response()->json(["status" => 1]);
    }
    public function delete_history($item_id){
        $history_item = VehicleHistory::findOrFail($item_id);
        $history_item->delete();
        return redirect()->back()->with('message','TMP Vehicle Booking History Item Deleted Successfully');
    }

}
