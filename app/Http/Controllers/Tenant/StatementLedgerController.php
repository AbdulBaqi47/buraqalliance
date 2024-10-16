<?php

namespace App\Http\Controllers\Tenant;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account_relation;
use App\Accounts\Models\Account_transaction;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Investor;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\StatementLedgerItem;
use App\Models\Tenant\StatementLedgerItemGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use function PHPUnit\Framework\isEmpty;

class StatementLedgerController extends Controller
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
     * View Page of driver ledger
     *
     */
    public function ViewDriverLedger($id)
    {
        $namespace = "driver";
        $driver = Driver::findOrFail((int)$id);
        $drivers = Driver::all();

        $title = $driver->full_name;

        return view('Tenant.statementledger.view', compact('namespace', 'id', 'driver', 'title','drivers'));
    }

    /**
     * View Page of company ledger
     *
     */
    public function ViewCompanyLedger($id)
    {
        $namespace = "company";
        $driver = Driver::findOrFail((int)$id);
        $drivers = Driver::all();

        $title = $driver->full_name;

        return view('Tenant.statementledger.view', compact('namespace', 'id', 'driver', 'title','drivers'));
    }

    /**
     * Create Page of Vehicle Ledger - Transactoin
     *
    */
    public function showTransactionForm(Request $request, $id, $transaction_type='global', $config=null)
    {
        $namespace = $request->get('namespace', null);

        $ledgerIds = StatementLedger::forNamespace($namespace)
        ->get()
        ->pluck('_id')
        ->toArray();

        $types = StatementLedgerItem::where('tag', 'manual_transaction')
        ->whereIn('statement_ledger_id', $ledgerIds)
        ->select('title')
        ->get()
        ->map(function ($item) {
            $item->title = strtolower($item->title);
            return $item;
        })
        ->keyBy('title')
        ->keys()
        ->map(function ($item) {
            return ucfirst($item);
        });

        return view('Tenant.statementledger.transaction.create', compact('config', 'types', 'id', 'transaction_type'));
    }

    /**
     * POST request of creating the transaction (Cash-Pay)
     *
    */
    public function create_transaction_cashpay(Request $request)
    {
        # This request involve cash, we better validate accounts
        if(!AccountGateway::validateBalance('amount')) return;

        $request->merge(['action' => "dr"]);
        $request->merge(['type' => "Cash paid"]);
        $request->merge(['deduct_account' => "on"]);
        $request->merge(['tag' => "cash_paid"]);

        return $this->create_transaction($request);
    }

    /**
     * POST request of creating the transaction (Cash-Receive)
     *
    */
    public function create_transaction_cashreceive(Request $request)
    {

        $request->merge(['action' => "cr"]);
        $request->merge(['type' => "Cash received"]);
        $request->merge(['deduct_account' => "on"]);
        $request->merge(['tag' => "cash_received"]);

        return $this->create_transaction($request);
    }

    /**
     * POST request of creating the transaction (Global)
     *
     */
    public function create_transaction(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|max:255',
            'amount' => 'required|numeric|gt:0',
            'namespace' => 'required|max:255',
            'type' => 'required|max:255',
            'given_date' => 'required|date'
        ]);

        $amount = $request->has('amount') ? (float)$request->amount : 0;
        $deductfromaccount = $request->has('deduct_account') ? true : false;
        $date = Carbon::parse($request->given_date)->format('Y-m-d');
        $month = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');

        $tag = $request->get('tag', 'manual_transaction');
        if(trim($tag) === '')$tag='manual_transaction';

        $vLedger = new StatementLedger;
        $exists = StatementLedger::ofNamespace($request->namespace, $request->resource_id)->first();;
        if(isset($exists)) $vLedger = $exists;
        else{

            $vLedger->linked_to = $request->namespace;
            $vLedger->linked_id = (int)$request->resource_id;
            $vLedger->save();
        }

        $vItemObj = (object)[
            'title' => $request->type,
            'description' => $request->description,
            'type' => $request->action,
            'tag' =>  $tag ?? 'manual_transaction',
            'date' => $date,
            'month' => $month,
            'amount' => $amount
        ];


        #need to check if image added
        if ($request->hasFile('attachment')) {
            $filepath = Storage::putfile('statement_ledgers', $request->file('attachment'));
            $vItemObj->attachment = $filepath;
        }

        $vLedgerItem =  $vLedger->addItem($vItemObj);

        if ($request->hasFile('attachment') && isset($vLedgerItem->attachment)) {
            $vItemObj->attachment = Storage::url($vLedgerItem->attachment);

        }

        if ($deductfromaccount) {

            $selected_account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();


            # Save ledger
            $ledger = new Ledger;
            $ledger->type=$request->action;
            $ledger->source_id=$vLedgerItem->_id;
            $ledger->source_model=get_class($vLedgerItem);
            $ledger->date=$date;
            $ledger->month = $month; // For Filteration Purpose
            $ledger->tag="statementledger_transaction";
            $ledger->is_cash=true;
            $ledger->amount=$amount;
            $ledger->props=[
                'by'=>Auth::user()->id,
                'account'=>[
                    'id'=>$selected_account->_id,
                    'title'=>$selected_account->title
                ]
            ];
            $ledger->save();


            #create account transaction
            $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
                'type'=>$request->action,
                'title'=>"Driver Statement" . " | " . $vLedgerItem->title,
                'description'=>(isset($vLedgerItem->description) && $vLedgerItem->description !== "" ? $vLedgerItem->description.' | ' : "") .Carbon::parse($month)->format('M Y'),
                'tag'=>'statementledger_transaction',
                'date' => $date,
                'amount'=>$amount,
                'additional_details' => [
                    "statement_ledger_id" => $vLedgerItem->statement_ledger_id,
                    "driver_id" => $vLedger->linked_id,
                    "type" => $request->namespace,
                    'attachment' => isset($vItemObj->attachment) ? $vItemObj->attachment : null
                ],
                'links' => [
                    [
                        'modal' => get_class(new StatementLedgerItem),
                        'id' => $vLedgerItem->_id,
                        'tag' => 'statementledger_transaction'
                    ],
                    [
                        'modal' => get_class(new Ledger),
                        'id' => $ledger->id,
                        'tag' => 'ledger'
                    ]
                ]
            ]);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $transaction->id;
            $relation->source_model = get_class($transaction);
            $relation->tag = 'transaction';
            $relation->is_real = false;
            $relation->save();

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'statementledger_transaction';
            $relation->is_real = true;
            $relation->save();
        } else {
            # Just add the transaction

            # Save ledger
            $ledger = new Ledger;
            $ledger->type=$request->action;
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


        return response()->json($vItemObj);
    }

    /**
     * This will show any extra view linked to this entry
     *  : This could be addon breakdown for addon_charge entries
     *
     */
    public function show_linked_view($id)
    {
        $vehicle_ledger_item = StatementLedgerItem::with('ledger.relations')->findOrFail($id);

        $view = request()->get('view', 'addon_breakdown');


        # ----------------------
        # VIEW: Addon Breakdown
        # ----------------------
        if($view === 'addon_breakdown'){

            $addon_id = null;

            # Find addon_charge relation from relations
            # : there we will find the addon_id on source key
            if(isset($vehicle_ledger_item->ledger) && isset($vehicle_ledger_item->ledger->relations)){
                $addon_charge_relation = $vehicle_ledger_item->ledger->relations->where('tag', 'addon_charge')->first();
                if(isset($addon_charge_relation) && isset($addon_charge_relation->source)){
                    $addon_id = $addon_charge_relation->source->addon_id;
                }
            }

            if(isset($addon_id)){
                // Render the view of addon breakdown
                request()->merge(['view' => "inline_statement"]);

                return (new AddonsController())->showBreakDownView(request(), $addon_id);
            }

        }

        # ----------------------
        # VIEW: Vehicle Bill Breakdown
        # ----------------------
        if($view === 'vehiclebills_breakdown'){

            $ledger_id = null;

            # Find addon_charge relation from relations
            # : there we will find the addon_id on source key
            if(isset($vehicle_ledger_item->ledger) && isset($vehicle_ledger_item->ledger->relations)){
                $ledger_id = $vehicle_ledger_item->ledger->id;
            }
            else{
                # Try to find ledger via relations
                $table_relation = $vehicle_ledger_item->table_relations()
                ->limit(1)
                ->first();

                if(isset($table_relation)){
                    $ledger_id = $table_relation->ledger->id;
                }
            }

            if(isset($ledger_id)){
                return (new VehicleBillController())->showBreakDownView(request(), $ledger_id);
            }

        }

        return abort(422, 'INVALID VIEW');

    }

    /**
     * Edit Popup of vehicle ledger
     *
     */
    public function showEditForm(Request $request, $id)
    {
        $ledger_item = StatementLedgerItem::with([
            'ledger'
        ])
        ->findOrFail($id);
        $resource_id = (int) $request->get('resource_id', null);

        $account_id = null;

        if(isset($ledger_item->ledger) && isset($ledger_item->ledger->props) && isset($ledger_item->ledger->props['account'])){
            $account_id = $ledger_item->ledger->props['account']['id'];
        }

        $ledger_item->account_id = $account_id;

        $booking_options = Driver::where('id', '!=', $resource_id)
        ->get();

        # Call the load function
        return $this->showTransactionForm($request, $resource_id, 'global', (object)[
            'ledger_item' => $ledger_item,
            'driver_options' => $booking_options,
            'action'=>'edit'
        ]);
    }

    /**
     * POST request of Editing Statement ledger
     *
     */
    public function updateStatementLedger(Request $request)
    {
        $statement_ledger_item_id = $request->get('statement_ledger_item_id', null);
        $statement_ledger_item = StatementLedgerItem::with([
            'statement_ledger',
            'ledger.relations'
        ])
        ->findOrFail($statement_ledger_item_id);

        $request->validate([
            'resource_id' => 'required|max:255',
            'amount' => 'required|numeric|gt:0',
            'namespace' => 'required|max:255',
            'type' => 'required|max:255',
            'given_date' => 'required|date'
        ]);

        # -----------------------------
        #      Generating Payload
        # -----------------------------

        $amount = $request->has('amount') ? (float)$request->amount : 0;
        $deductfromaccount = $request->has('deduct_account') ? true : false;
        $date = Carbon::parse($request->given_date)->format('Y-m-d');
        $month = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');
        $changeDriver = $request->has('change_driver') ? true : false;

        $tag = $request->get('tag', 'manual_transaction');
        if(trim($tag) === '')$tag='manual_transaction';

        $feed = [];

        # -----------------------------
        #  Update vehicle ledger item
        # -----------------------------
        $dLedger = $statement_ledger_item->statement_ledger;

        $vItemObj = (object)[
            'title' => $request->type,
            'description' => $request->description,
            'type' => $request->action,
            'tag' =>  $tag ?? 'manual_transaction',
            'date' => $date,
            'month' => $month,
            'amount' => $amount
        ];

        #need to check if image added
        if ($request->hasFile('attachment')) {
            $filepath = Storage::putfile('statement_ledgers', $request->file('attachment'));
            $vItemObj->attachment = $filepath;
        }
        else{
            // Remove attachment
            $vItemObj->attachment = null;
        }

        # Change Driver (If required)
        if($changeDriver){
            $new_driver_id = $request->get('driver_id', null);
            if(isset($new_driver_id)){
                # Find Statement ledger against this Driver
                $new_statement_ledger = StatementLedger::ofNamespace($request->namespace, $new_driver_id)->first();
                if(isset($new_statement_ledger)){
                    $vItemObj->statement_ledger_id = $new_statement_ledger->id;
                    $vItemObj->driver_id = $new_driver_id;
                }
                else{
                    // throw new Exception("Statement Ledger not found against this Driver!");
                    $dLedger = new StatementLedger;
                    $dLedger->linked_to = $request->namespace;
                    $dLedger->linked_id = (int)$new_driver_id;
                    $dLedger->save();
                    $vItemObj->statement_ledger_id = $dLedger->id;
                    $vItemObj->driver_id = $new_driver_id;
                }

            }
        }

        # Update the item and variable
        $new_statement_ledger_item = $dLedger->updateItem($statement_ledger_item_id, $vItemObj);

        # If new attachment found, update the attachment url to it became direct url
        if ($request->hasFile('attachment') && isset($new_statement_ledger_item->attachment)) {
            $new_statement_ledger_item->attachment = Storage::url($new_statement_ledger_item->attachment);
        }

        $feed[] = [
            'action' => 'updated',
            'table' => $new_statement_ledger_item->getTable(),
            'data' => $new_statement_ledger_item->toArray()
        ];

        # Find Ledger
        $ledger = null;
        $direct_ledger_found = false;
        if(isset($statement_ledger_item->ledger)){
            $ledger = $statement_ledger_item->ledger;
            $direct_ledger_found = true;
        }
        else{
            # Try to find ledger via relations
            $table_relation = $statement_ledger_item->table_relations()
            ->limit(1)
            ->first();

            if(isset($table_relation)){
                $ledger = $table_relation->ledger;

                // Eager-load relations
                $ledger->load([
                    'relations'
                ]);
            }
        }

        # -----------------------------
        #  Update Ledger
        #   : If entry channel is import, skip
        #   : skip it, because entries imported
        #   : are not linked to 1 entry
        #
        #  Update/Create Account Transaction
        #   : FIND: transaction linked to ledger (ledger->props->account->id)
        # -----------------------------

        if(isset($ledger)){
            if($direct_ledger_found){
                $selected_account = null;

                if($deductfromaccount){
                  $selected_account =  \App\Accounts\Handlers\AccountGateway::getSelectedAccount();
                }
                # Save ledger
                $new_ledger = Ledger::find($ledger->id);
                $new_ledger->type=$request->action;
                $new_ledger->source_id=$statement_ledger_item->id;
                $new_ledger->source_model=get_class($statement_ledger_item);
                $new_ledger->date=$date;
                $new_ledger->month = $month; // For Filteration Purpose
                $new_ledger->tag="statementledger_transaction";
                $new_ledger->is_cash=$deductfromaccount;
                $new_ledger->amount=$amount;

                $props = $new_ledger->props;
                if($deductfromaccount){
                    $props['account'] = [
                        'id'=>$selected_account->_id,
                        'title'=>$selected_account->title
                    ];
                }
                else{
                    if(isset($props['account'])) unset($props['account']);
                }
                $new_ledger->props=$props;

                $new_ledger->update();

                $feed[] = [
                    'action' => 'updated',
                    'table' => $new_ledger->getTable(),
                    'data' => $new_ledger->toArray()
                ];

                # ------------------
                # Update via ledger
                # ------------------
                $ledger_relations = $ledger->relations;

                $transaction = null;
                foreach ($ledger_relations as $relation) {
                    # Skip editing Statement ledger item since we already edited it above
                    if($relation->tag == "statementledger_transaction" && $relation->source_id == $statement_ledger_item->id) continue;


                    if($relation->source_model === "App\Accounts\Models\Account_transaction") {
                        if(isset($relation->tag) && $relation->tag !== "external"){
                            $transaction_id=$relation->source_id;

                            if($deductfromaccount){
                                #Edit account transaction
                                $transaction = AccountGateway::edit_transaction([
                                    'type'=>$request->action,
                                    'title'=> "Statement Ledger". " | " . $new_statement_ledger_item->title,
                                    'description'=>(isset($new_statement_ledger_item->description) && $new_statement_ledger_item->description !== "" ? $new_statement_ledger_item->description.' | ' : "") .Carbon::parse($month)->format('M Y'),
                                    'tag'=>'statementledger_transaction',
                                    'date' => $date,
                                    'amount'=>$amount,
                                    'account_id'=>$selected_account->_id,
                                    'transaction_id'=>$transaction_id,
                                    'additional_details' => [
                                        "statement_ledger_id" => $new_statement_ledger_item->statement_ledger_id,
                                        "driver_id" => (int)$new_driver_id,
                                        "type" => $request->namespace,
                                        'attachment' => isset($vItemObj->attachment) ? $vItemObj->attachment : null
                                    ],
                                    'links' => [
                                        [
                                            'modal' => get_class(new StatementLedgerItem),
                                            'id' => $new_statement_ledger_item->_id,
                                            'tag' => 'statementledger_transaction'
                                        ],
                                        [
                                            'modal' => get_class(new Ledger),
                                            'id' => $ledger->id,
                                            'tag' => 'ledger'
                                        ]
                                    ]
                                ]);

                                $feed[] = [
                                    'action' => 'updated',
                                    'table' => $transaction->getTable(),
                                    'data' => $transaction->toArray()
                                ];
                            }
                            else{
                                $transaction = Account_transaction::find($transaction_id);

                                # Delete link
                                $relation->forceDelete();
                            }


                        }
                    }
                    else{
                        # Update source
                        $source = $relation->source;

                        if(isset($source->amount)) $source->amount=$amount;
                        if(isset($source->date)) $source->date=$date;
                        if(isset($source->given_date)) $source->given_date=$date;
                        if(isset($source->month)) $source->month=$month;

                        # For transfer entries, don't update the description
                        if($relation->tag != "interbooking_transfer_out" && $relation->tag != "interbooking_transfer_in"){
                            if(isset($source->description)) $source->description=$new_statement_ledger_item->description ?? null;
                        }
                        $source->update();

                        $feed[] = [
                            'action' => 'updated',
                            'table' => $source->getTable(),
                            'data' => $source->toArray()
                        ];
                    }


                }

                # ----------------------------
                # Create/Delete Transaction
                # ----------------------------

                # If transaction not found and "Adjust in accounts" seleceted
                # Create transaction
                if(!isset($transaction) && $deductfromaccount){
                    // Means transaction not found in ledger relations
                    // It seems this entry has not linked to accounts previously

                    $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
                        'type'=>$request->action,
                        'title'=> "Statement Ledger" . " | " . $new_statement_ledger_item->title,
                        'description'=>(isset($new_statement_ledger_item->description) && $new_statement_ledger_item->description !== "" ? $new_statement_ledger_item->description.' | ' : "") .Carbon::parse($month)->format('M Y'),
                        'tag'=>'statementledger_transaction',
                        'date' => $date,
                        'amount'=>$amount,
                        'additional_details' => [
                            "statement_ledger_id" => $new_statement_ledger_item->statement_ledger_id,
                            "driver_id" => (int)$new_driver_id,
                            "type" => $request->namespace,
                            'attachment' => isset($vItemObj->attachment) ? $vItemObj->attachment : null
                        ],
                        'links' => [
                            [
                                'modal' => get_class(new StatementLedgerItem),
                                'id' => $new_statement_ledger_item->_id,
                                'tag' => 'statementledger_transaction'
                            ],
                            [
                                'modal' => get_class(new Ledger),
                                'id' => $ledger->id,
                                'tag' => 'ledger'
                            ]
                        ]
                    ]);

                    $feed[] = [
                        'action' => 'created',
                        'table' => $transaction->getTable(),
                        'data' => $transaction->toArray()
                    ];

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $transaction->id;
                    $relation->source_model = get_class($transaction);
                    $relation->tag = 'transaction';
                    $relation->is_real = false;
                    $relation->save();

                }

                # If transaction found and "Adjust in accounts" not seleceted
                # Delete the transaction
                if(!$deductfromaccount && isset($transaction)){
                    # Delete account entry
                    $transaction->links()->forceDelete();

                    $feed[] = [
                        'action' => 'deleted',
                        'table' => $transaction->getTable(),
                        'data' => $transaction->toArray()
                    ];

                    $transaction->forceDelete();
                }

            }
            else{
                # --------------------------------
                #  Entry was imported
                #   : For some entries,
                #   : we need to edit other tables
                # --------------------------------

                if($ledger->tag === 'client_income' && $ledger->source_model === TransactionLedger::class && isset($statement_ledger_item->driver_id)){
                    # Edit transaction ledger detail item
                    # against this item
                    #   : Find via driver ID

                    $transaction_ledger_detail = TransactionLedgerDetails::where('tl_id', $ledger->source_id)
                    ->where('source_id', $statement_ledger_item->driver_id)
                    ->where('source_model', Driver::class)
                    ->limit(1)
                    ->first();

                    if(isset($transaction_ledger_detail)){
                        $transaction_ledger_detail->amount = $amount;
                        $transaction_ledger_detail->update();

                        $feed[] = [
                            'action' => 'updated',
                            'table' => $transaction_ledger_detail->getTable(),
                            'data' => $transaction_ledger_detail->toArray()
                        ];
                    }

                }


            }
        }

        return response()->json([
            'status' => 1,
            'feed' => $feed
        ]);
    }

    /**
     * DELETE request of vehicle ledger
     *
     */
    public function deleteVehicleLedger($vehicle_ledger_item_id)
    {
        $vehicle_ledger_item = StatementLedgerItem::with([
            'ledger.relations'
        ])
        ->findOrFail($vehicle_ledger_item_id);

        # Find Ledger
        $ledger = null;
        $direct_ledger_found = false;
        if(isset($vehicle_ledger_item->ledger)){
            $ledger = $vehicle_ledger_item->ledger;
            $direct_ledger_found = true;
        }
        else{
            # Try to find ledger via relations
            $table_relation = $vehicle_ledger_item->table_relations()
            ->limit(1)
            ->first();

            if(isset($table_relation)){
                $ledger = $table_relation->ledger;

                // Eager-load relations
                $ledger->load([
                    'relations'
                ]);
            }
        }

        $feed = [];

        if(isset($ledger)){

            if($direct_ledger_found){
                # ------------------
                # Delete via ledger
                # ------------------
                $ledger_relations = $ledger->relations;

                foreach ($ledger_relations as $relation) {
                    # Skip editing vehicle ledger item since we already edited it above
                    if($relation->tag == "statementledger_transaction" && $relation->source_id == $vehicle_ledger_item->id) continue;

                    # Delete source
                    $source = $relation->source;

                    # If not found, means its already deleted
                    if(!isset($source)) {

                        # Delete relation too
                        $relation->delete();

                        continue;
                    }

                    if($relation->source_model === "App\Accounts\Models\Account_transaction") {
                        # Wee need to delete the links of this transaction too
                        $source->links()->delete();
                    }
                    if($relation->source_model === Ledger::class) {
                        # Wee need to delete the relations of this ledger too
                        $source->relations()->delete();
                    }

                    $feed[] = [
                        'action' => 'deleted',
                        'table' => $source->getTable(),
                        'data' => $source->toArray()
                    ];

                    $source->delete();

                    # Delete relation too
                    $relation->delete();
                }

                $feed[] = [
                    'action' => 'deleted',
                    'table' => $ledger->getTable(),
                    'data' => $ledger->toArray()
                ];

                # Delete Ledger
                $ledger->delete();
            }
            else{
                # --------------------------------
                #  Entry was imported
                #   : For some entries,
                #   : we need to edit other tables
                # --------------------------------

                if($ledger->tag === 'client_income' && $ledger->source_model === TransactionLedger::class && isset($vehicle_ledger_item->driver_id)){
                    # Edit transaction ledger detail item
                    # against this item
                    #   : Find via driver ID

                    $transaction_ledger_detail = TransactionLedgerDetails::where('tl_id', $ledger->source_id)
                    ->where('source_id', $vehicle_ledger_item->driver_id)
                    ->where('source_model', Driver::class)
                    ->limit(1)
                    ->first();

                    if(isset($transaction_ledger_detail)){
                        $feed[] = [
                            'action' => 'deleted',
                            'table' => $transaction_ledger_detail->getTable(),
                            'data' => $transaction_ledger_detail->toArray()
                        ];

                        $transaction_ledger_detail->delete();

                    }

                }


            }

        }

        $feed[] = [
            'action' => 'deleted',
            'table' => $vehicle_ledger_item->getTable(),
            'data' => $vehicle_ledger_item->toArray()
        ];

        # Delete vehicle_ledger_item
        $vehicle_ledger_item->delete();

        return response()->json([
            'status' => 1,
            'feed' => $feed
        ]);
    }

    public function transferBalance(Request $request, int $booking_id)
    {
        $booking = VehicleBooking::findOrFail($booking_id);
        $booking_options = VehicleBooking::select('id', 'investor_id', 'notes', 'date', 'initial_amount', 'status')
        ->with([
            'investor' => function($query){
                $query->select('id', 'name');
            },
            'vehicle' => function($query){
                $query->select('vehicle_booking_id','plate');
            },
            'drivers' => function($query){
                $query->select('id', 'booking_id', 'name');
            }
        ])
        ->where('id', '!=', $booking_id)
        ->get()
        ->map(function ($item) {
            $prefix = $item->status === 'open' ? "B#$item->id" : "V#$item->id / " . $item->vehicle->plate;
            $drivers = $item->drivers->map(function($driver){
                return $driver->full_name;
            })
            ->values()
            ->toArray();

            $driverList = '';
            if(count($drivers) > 0){
                $driverList = ' (Drivers: '.(implode(' | ', $drivers)).')';
            }
            return [
                'id' => $item->id,
                'text' => $prefix. ' / '.$item->investor->name . $driverList,
                'selected' => false
            ];
        });
        return view('Tenant.booking.transfer_amount', compact('booking_options', 'booking'));
    }

    public function transferBalanceAction(Request $request, int $booking_id)
    {
        // Validate Request Data
        $request->validate([
            'amount' => ['gt:0', 'required', 'numeric'],
            'booking' => ['required', 'numeric'],
            'given_date' => 'required|date',
            'month' => 'required|date'
        ]);

        if($request->has('driver_id')){
            $driver = Driver::findOrFail((int)$request->driver_id);
        }

        $amount = (int) $request->amount; // Get Numeric Value Of Amount
        $target_booking_id = (int) $request->booking; // Get Numeric Value Of Target Booking ID
        // Get Source and Target Bookings and Their Ledgers
        $date = Carbon::parse($request->given_date)->format('Y-m-d');
        $month = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');
        $source_booking = VehicleBooking::with('vehicle_ledgers','vehicle','investor')->findOrFail($booking_id);
        $source_investor = $source_booking->investor;
        $source_vehicle = $source_booking->vehicle;
        $target_booking = VehicleBooking::with('vehicle_ledgers','vehicle','investor')->findOrFail($target_booking_id);
        $target_investor = $target_booking->investor;
        $target_vehicle = $target_booking->vehicle;
        $source_ledger = $source_booking->vehicle_ledgers->first();
        $target_ledger = $target_booking->vehicle_ledgers->first();
        $source_type = $source_booking->status === 'closed' ? "V#$source_booking->id / $source_vehicle->plate":"B#$source_booking->id";
        $target_type = $target_booking->status === 'closed' ? "V#$target_booking->id / $target_vehicle->plate":"B#$target_booking->id";
        // Create TXN Array Which Contains Transaction Details
        $txn = (object) [
            'title' => "Amount Transfer Between Bookings",
            'date' => $date,
            'month' => $month,
            'amount' => $amount,
            'channel' => "app",
        ];
        $extra = "";
        if(isset($driver)){
            $txn->driver_id = $driver->id;
            $extra = "Driver: $driver->full_name <br/>";
        }
        #need to check if image added
        if ($request->hasFile('attachment')) {
            $filepath = Storage::putFile('vehicle_ledgers', $request->file('attachment'));
            $txn->attachment = $filepath;
        }
        // For Source Type Will Be DR as We Are Deducting Amount From Source :: This Wil Create Ledger Item and Return in $source_ledger_item
        $txn->type = 'dr';
        $txn->tag = "interbooking_transfer_out";
        $txn->description = "Transfer To ( $target_investor->name ) - $target_type <br/> $request->description";
        $source_ledger_item = $source_ledger->addItem($txn);
        // For Target Type Will Be CR as We Are Adding Amount To Source :: This Wil Create Ledger Item and Return in $target_ledger_item
        $txn->type = 'cr';
        $txn->tag = 'interbooking_transfer_in';
        $txn->description = "Received From ( $source_investor->name ) $source_type <br/> $extra $request->description";
        unset($txn->driver_id);
        $target_ledger_item = $target_ledger->addItem($txn);
        // Create Ledger Items For Source
        $source_ledger = new Ledger;
        $source_ledger->type=$source_ledger_item->type;
        $source_ledger->source_id=$source_ledger_item->_id;
        $source_ledger->source_model=get_class($source_ledger_item);
        $source_ledger->date = $date;
        $source_ledger->month = Carbon::now()->format('Y-m-d'); // For Filteration Purpose
        $source_ledger->tag=$source_ledger_item->tag;
        $source_ledger->is_cash=false;
        $source_ledger->amount=$amount;
        $props = [
            'by'=>Auth::user()->id
        ];

        if(isset($driver)){
            $props['prefix'] = [
                'text' => $driver->full_name,
                'url' => route('tenant.admin.drivers.viewDetails', $driver->id),
            ];
        }
        $source_ledger->props = $props;
        $source_ledger->save();
        // Create Ledger Items For Target
        $target_ledger = new Ledger;
        $target_ledger->type=$target_ledger_item->type;
        $target_ledger->source_id=$target_ledger_item->_id;
        $target_ledger->source_model=get_class($target_ledger_item);
        $target_ledger->date = $date;
        $target_ledger->month = Carbon::now()->format('Y-m-d'); // For Filteration Purpose
        $target_ledger->tag=$target_ledger_item->tag;
        $target_ledger->is_cash=false;
        $target_ledger->amount=$amount;
        $target_ledger->props=[
            'by'=>Auth::user()->id
        ];
        $target_ledger->save();
        // Create Table Relations For Both Ledger Entries
        $source_relation = new Table_relation;
        $source_relation->ledger_id = $source_ledger->id;
        $source_relation->source_id = $source_ledger_item->id;
        $source_relation->source_model = get_class($source_ledger_item);
        $source_relation->tag = $source_ledger_item->tag;
        $source_relation->is_real = false;
        $source_relation->save();

        $target_relation = new Table_relation;
        $target_relation->ledger_id = $target_ledger->id;
        $target_relation->source_id = $target_ledger_item->id;
        $target_relation->source_model = get_class($target_ledger_item);
        $target_relation->tag = $target_ledger_item->tag;
        $target_relation->is_real = false;
        $target_relation->save();

        // Linking Both Ledger Entries
        $source_relation_x = new Table_relation;
        $source_relation_x->ledger_id = $source_ledger->id;
        $source_relation_x->source_id = $target_ledger->id;
        $source_relation_x->source_model = get_class($target_ledger);
        $source_relation_x->tag = $source_ledger_item->tag;
        $source_relation_x->is_real = false;
        $source_relation_x->save();

        $target_relation_y = new Table_relation;
        $target_relation_y->ledger_id = $target_ledger->id;
        $target_relation_y->source_id = $source_ledger->id;
        $target_relation_y->source_model = get_class($source_ledger);
        $target_relation_y->tag = $target_ledger_item->tag;
        $target_relation_y->is_real = false;
        $target_relation_y->save();

        // Linking Both Entries To Ledgers Too
        $source_relation_xx = new Table_relation;
        $source_relation_xx->ledger_id = $source_ledger->id;
        $source_relation_xx->source_id = $target_ledger_item->_id;
        $source_relation_xx->source_model = get_class($target_ledger_item);
        $source_relation_xx->tag = $source_ledger_item->tag;
        $source_relation_xx->is_real = false;
        $source_relation_xx->save();

        $target_relation_yy = new Table_relation;
        $target_relation_yy->ledger_id = $target_ledger->id;
        $target_relation_yy->source_id = $source_ledger_item->_id;
        $target_relation_yy->source_model = get_class($source_ledger_item);
        $target_relation_yy->tag = $target_ledger_item->tag;
        $target_relation_yy->is_real = false;
        $target_relation_yy->save();

        return response()->json(['message'=>true]);
    }

    /*
        Show Ledger Item details Popup
    */
    public function show_details($id) {
        $ledger_item = StatementLedgerItem::with('ledger')->findOrFail($id);

        # Find Ledger
        $ledger = null;
        if(isset($ledger_item->ledger)){
            $ledger = $ledger_item->ledger;
        }
        else{
            # Try to find ledger via relations
            $table_relation = $ledger_item->table_relations()
            ->limit(1)
            ->first();

            if(isset($table_relation)){
                $ledger = $table_relation->ledger;
            }
        }

        # Find Added by user
        $by = null;
        if(isset($ledger->props['by'])){
            $user = User::find($ledger->props['by']);
            if(isset($user)) $by = $user->name;
        }

        # Find account
        $account_name = null;
        if( isset($ledger->props) && isset($ledger->props['account']) && isset($ledger->props['account']['title'])){
            $account_name = $ledger->props['account']['title'];
        }


        return view('Tenant.statementledger.details',compact('ledger_item', 'by', 'account_name', 'ledger'));
    }

    // ----------------------------
    //    Group Setting Methods
    // ----------------------------

    /**
     * View Page of Group
     *
    */
    public function viewGroups()
    {
        return view('Tenant.statementledger.groups.view');
    }

    /**
     * Create Page of Group
     *
    */
    public function showGroupForm($config=null)
    {
        $groups = StatementLedgerItemGroup::all();

        $all_tags = StatementLedgerItem::select('tag')
        ->groupBy('tag')
        ->pluck('tag')
        ->map(function ($item) {
            return [
                'id' => strtolower($item),
                'text' => strtolower($item)
            ];
        });


        return view('Tenant.statementledger.groups.create', compact('config', 'groups', 'all_tags'));
    }

    /**
     * POST request of creating the Setting
     *
    */
    public function create_group(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:statementledger_group_id|max:255',
            'tags' => 'required|array|max:255',
        ]);


        $tags = [];
        foreach ($request->get('tags', []) as $tag) {
            $tags[] = [
                'title' => trim(strtolower($tag['title']))
            ];
        }

        $group = new StatementLedgerItemGroup;
        $group->title = $request->get('title');
        $group->driver_based = $request->has('driver_based') ? true : false;
        $group->collapse = $request->has('collapse') ? true : false;
        $group->date_override = $request->get('date_override', null);
        $group->tags = count($tags) > 0 ? $tags : null;

        $group->save();

        return response()->json([
            'status' => 1
        ]);

    }

    /**
     * GET request of editing the page
     *
    */
    public function showGroupEditForm($id)
    {
        # Find the job
        $group = StatementLedgerItemGroup::findOrFail($id);

        $group->actions=[
            'status'=>1,
        ];

        # Call the load job function
        return $this->showGroupForm((object)[
            'group'=>$group,
            'action'=>'edit'
        ]);

    }

    public function edit_group(Request $request)
    {
        $group = StatementLedgerItemGroup::findOrFail($request->statementledger_group_id);

        $request->validate([
            'title' => [
                'required',
                'max:255',
                Rule::unique('statementledger_group_id')->ignore($group->_id, '_id'),
            ],
            'tags' => 'required|array|max:255',
        ]);


        $tags = [];
        foreach ($request->get('tags', []) as $tag) {
            $tags[] = [
                'title' => trim(strtolower($tag['title']))
            ];
        }

        $group->title = $request->get('title');
        $group->driver_based = $request->has('driver_based') ? true : false;
        $group->collapse = $request->has('collapse') ? true : false;
        $group->date_override = $request->get('date_override', null);
        $group->tags = count($tags) > 0 ? $tags : null;

        $group->update();

        return response()->json([
            'status' => 1
        ]);
    }
}
