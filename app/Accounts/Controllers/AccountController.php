<?php

namespace App\Accounts\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Accounts\Models\Account;
use Auth;
use Carbon\Carbon;
use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account_transaction;
use App\Accounts\Models\Account_relation;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Installment;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {

    }

    /**
     * Add Account
     *
    */
    public function showAccountsForm()
    {
        $accounts = Account::all();
        $otherdeps = $accounts->where('department', '!=', 'bank')
        ->where('department', '!=', 'cih')
        ->keyBy('department')
        ->keys();
        // return $otherdeps;
        return view('Accounts.create', compact('accounts', 'otherdeps'));
    }


    /**
     * Create Account
     *
    */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|max:255',
            // 'type' => 'required|max:255',
            'title' => 'required|max:255',
        ]);

        # Payload
        $dep = $request->department;
        $type = $request->type;
        $title = $request->title;

        if($dep!='bank'&&$dep!='cih')$type='other';

        $handle = $dep.'-'.$type.'-'.$title;
        $handle = Str::slug($handle, '-');

        $exists = Account::where('handle', $handle)->exists();
        if($exists){

            throw ValidationException::withMessages(["handle" => "Account already exists with similer properties, try changing type, department and title"]);
            return;

        }

        #Saving account
        $account = new Account;
        $account->department=$dep;
        $account->handle=$handle;
        $account->type=$type;
        $account->title=$title;
        $account->status=1; # active
        $account->save();

        return response()->json($account);
    }

    /**
     * View Account
     *
    */
    public function ViewAccounts()
    {
        #all accounts grouped by departments
        $departments = AccountGateway::getGrantedAccounts()->groupBy('department');
        // return $departments;
        return view('Accounts.view', compact('departments'));
    }

    /**
     * View Account Transaction
     *
    */
    public function ViewAccountTransactions($account_id)
    {
        #Find Account
        $account = Account::find($account_id);

        // return response()->json($account);


        return view('Accounts.transactions.view', compact('account'));
    }


    /**
     * SHow Add transaction Form
     *
    */
    public function showTransactionForm($account_id){

        return view('Accounts.transactions.create', compact('account_id'));
    }

    /**
     * Create Account Transaction
     *
    */
    public function createTransaction(Request $request, $account_id)
    {
        # Basic Validation
        $validated = $request->validate([
            'amount' => 'required|gt:0',
        ]);

        # Payload
        $amount = (float)$request->amount;
        $description = $request->description;

        $transaction = AccountGateway::add_transaction([
            'account_id'=>$account_id,
            'type'=>'cr',
            'title'=>'Manual Transaction',
            'description'=>$description,
            'tag'=>'manual_transaction',
            'amount'=>$amount,
            'links'=>[ ]
        ]);

        # Reformatting transction to laod in table



        return $transaction;
    }

    public function getTransactions($account_id)
    {
        #Find Account Transactions
        $transactions = Account_transaction::with([
            'links' => function($query){
                $query->whereIn('subject_type', [Employee_ledger::class, Account_transaction::class]);
            },
            'user',
            'links.source.user'
        ])->where('account_id', $account_id)
        ->where('status', 'paid')
        ->get();

        # Calculate balance against each transaction
        $running_balance=0;

        # sort by asc (So we can calculate balance from start)
        $transactions = $transactions->sortBy('time');
        // $transactions = $transactions->sortBy(function ($item) {
        //     return $item->id;
        // });

        // return $transactions;
        foreach ($transactions as $transaction) {
            if($transaction->type=='dr'){
                $running_balance -= $transaction->amount;
            }
            else{
                $running_balance += $transaction->amount;
            }
            $transaction->balance_rendered = round($running_balance,2);
        }

        # Now sort back desc (So rows can be show from top to bottom)
        $transactions = $transactions->sortByDesc('time');
        // $transactions = $transactions->sortByDesc(function ($item) {
        //     return $item->id;
        // });

        return Datatables::of($transactions)
        ->addColumn('actions', function($transaction){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('dt_created_at', function($transaction){
            $causer= $transaction->user;
            $causer_name = 'No User';
            if (isset($causer)) {
                $causer_name = $causer->name;
            }

            # get local utc offset, so we can show transaction time according to user
            $utc_offset = request()->cookie('kra_utcoffset');
            if(!isset($utc_offset))$utc_offset=250; # default to dubai

            return Carbon::parse($transaction->time)->utcOffset($utc_offset)->format('d/M/Y').' - '.$causer_name;

        })
        ->addColumn('dt_details', function($transaction){
            $prefix = '';

            if($transaction->tag=="external"){
                # Transaction if from out of system, show transction ID and system
                if(isset($transaction->additional_details)){
                    $origin = isset($transaction->additional_details["origin"]) ? $transaction->additional_details['origin'] : "ds";
                    $transaction_id = $transaction->additional_details['transaction_id'];

                    $originTitle = $origin === "ds" ? "Delivery System"
                    : "";

                    $prefix = '
                    <span class="svg-icon kt-portlet__head-icon d-block transaction__prefix">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15px" height="15px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M3.52270623,14.028695 C2.82576459,13.3275941 2.82576459,12.19529 3.52270623,11.4941891 L11.6127629,3.54050571 C11.9489429,3.20999263 12.401513,3.0247814 12.8729533,3.0247814 L19.3274172,3.0247814 C20.3201611,3.0247814 21.124939,3.82955935 21.124939,4.82230326 L21.124939,11.2583059 C21.124939,11.7406659 20.9310733,12.2027862 20.5869271,12.5407722 L12.5103155,20.4728108 C12.1731575,20.8103442 11.7156477,21 11.2385688,21 C10.7614899,21 10.3039801,20.8103442 9.9668221,20.4728108 L3.52270623,14.028695 Z M16.9307214,9.01652093 C17.9234653,9.01652093 18.7282432,8.21174298 18.7282432,7.21899907 C18.7282432,6.22625516 17.9234653,5.42147721 16.9307214,5.42147721 C15.9379775,5.42147721 15.1331995,6.22625516 15.1331995,7.21899907 C15.1331995,8.21174298 15.9379775,9.01652093 16.9307214,9.01652093 Z" fill="#000000" fill-rule="nonzero" opacity="0.6"/>
                                </g>
                            </svg>
                            <strong>#'.$transaction_id.'</strong> on '.$originTitle.'
                        </span>
                    </span> ';
                }
            }



            $desc = $transaction->description;
            if(!isset($desc) || $desc==''){
                # return only title
                return $prefix . $transaction->title;
            }
            $add_user_details = isset($transaction->links[0]->source->user->name) ? ' ( ' . $transaction->links[0]->source->user->name . ' ) ' : '';
            $desc .= isset($transaction->links[0]->source) ? ' | ' . Carbon::parse(optional($transaction->links[0]->source)->month)->format('M Y') : '';
            # return title along with detailed description
            return $prefix . '<span class="transaction__desc-title">'.$transaction->title . $add_user_details . '</span><pre class="transaction__desc-subtitle">'.$desc.'</pre>';
        })
        ->addColumn('dt_cr', function($transaction){
            if($transaction->type=='cr')return $transaction->amount;
            return 0;
        })
        ->addColumn('dt_dr', function($transaction){
            if($transaction->type=='dr')return $transaction->amount;
            return 0;
        })
        ->addColumn('dt_balance', function($transaction){
            return $transaction->balance_rendered;
        })
        ->rawColumns(['dt_details'])
        ->make(true);


    }

    /**
     * Show Edit Transaction Form
     *
    */
    public function showTransactionEditForm($id, $config=null)
    {

        return view('Accounts.transactions.edit', compact('config', 'id'));
    }

    /**
     * POST request of Editing the transaction
     *
    */
    public function editTransaction(Request $request){

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|gt:0',
            'description' =>  'required'
        ]);

        $amount = (float)$request->amount;
        $description = $request->description;
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $id = (int) $request->transaction_id;
        $transaction = Account_transaction::with('links.source')->findOrFail($id);
        $transaction_links = $transaction->links;

        $feed = [];

        foreach ($transaction_links as $link) {
            // if request has from_account and to_account then proceed to condition
            if(isset($request->from_account) && isset($request->to_account)){
                $from_account = $request->from_account;
                $to_account = $request->to_account;
                $is_linked_transaction = $link->subject_type === Account_transaction::class;
                // Proceed If Transaction is Linked Transaction having subject type Account_transaction
                if($is_linked_transaction){
                    $source = $link->source; // Get Source Of Other Transaction Entry
                    if($transaction->type === 'dr'){
                        // if Transaction type is Debit
                        // cash is sent from this account
                        $transaction->account_id = $from_account;
                        $source->account_id = $request->to_account;
                    }else{
                        // if Transaction type is Credit
                        // Cash is Received in this account
                        $transaction->account_id = $to_account;
                        $source->account_id = $request->from_account;
                    }
                    $source->description=$description;
                }
            }

            # Update source
            $source = $link->source;
            if(isset($source->amount)) $source->amount=$amount;

            if(isset($source->date)) $source->date=$date;
            else if(isset($source->given_date)) $source->given_date=$date;
            else if(isset($source->time) && $link->subject_type === Account_transaction::class){
                $localUtcOffset = request()->cookie('localUtcOffset');
                if (!isset($localUtcOffset)) $localUtcOffset = 000;
                $source->time = Carbon::parse($date . ' ' . Carbon::now()->format('H:i:s'))->utcOffset($localUtcOffset)->toAtomString();
            }

            $source->save();

            $feed[] = [
                'model' => $link->subject_type,
                'id' => $link->subject_id,
                'tag' => $link->tag,
                'data' => $source
            ];
        }
        # Update Ledger
        $transaction->amount=$amount;

        $localUtcOffset = request()->cookie('localUtcOffset');
        if (!isset($localUtcOffset)) $localUtcOffset = 000;
        $time = Carbon::parse($date . ' ' . Carbon::now()->format('H:i:s'))->utcOffset($localUtcOffset)->toAtomString();
        $transaction->time=$time;
        $transaction->description=$description;

        $transaction->update();

        return response()->json([
            "status" => 1,
            'feed' => $feed
        ]);
    }

    /**
     * DELETE request of Delete the transaction
     *
    */
    public function deleteTransaction(int $id){

        $transaction = Account_transaction::with('links.source')->findOrFail($id);

        $links = $transaction->links;

        # This will return all tables records that are deleted
        $feed = [];

        foreach ($links as $link) {
            # Delete source
            $source = $link->source;

            $feed[] = [
                'model' => $link->subject_type,
                'id' => $link->subject_id,
                'tag' => $link->tag,
                'data' => $source
            ];

            if($link->source_model === Ledger::class) {
                # Wee need to delete the relations of this ledger too
                $source->relations()->delete();
            }

            $source->delete();

            # Delete relation too
            $link->delete();

        }
        # Delete transaction
        $transaction->delete();

        return response()->json([
            "status" => 1,
            'feed' => $feed
        ]);
    }

    /**
     * Account Transfer page
     *
    */
    public function showTransferForm(Request $request)
    {
        // $ledger = \App\Models\Tenant\Ledger::with('relations')->find(17);

        // // return $ledger;

        // $ledger_relations = $ledger->relations;

        // foreach ($ledger_relations as $relation) {
        //     # delete source
        //     $relation->source->delete();

        //     # delete relation
        //     $relation->delete();
        // }
        // # Delete ledger
        // $ledger->delete();


        if(!$request->has('dep')){
            # We need a department from which you need to transfer
            abort(505, "Department is required");
        }

        $dep = $request->get('dep');

        # Find accounts of this department
        $accountsData = AccountGateway::getGrantedAccounts();
        $self_accounts=$accountsData->where('department', $dep)->groupBy('department');

        $all_accounts = $accountsData->groupBy('department');

        // return $accounts;
        return view('Accounts.transfer.create', compact('all_accounts', 'self_accounts', 'accountsData'));
    }

    public function create_transfer(Request $request)
    {
        # Basic Validation
        $request->validate([
            'amount'    => 'required|gt:0',
            'from'      =>  'required|max:255',
            'to'      =>  'required|max:255|different:from',
            'date'=> 'required',
        ]);


        # Payload
        $from_id = $request->from;
        $to_id = $request->to;
        $amount = (float)$request->amount;
        $transfer_date=$request->date;
        $description=null;
        if(isset($request->description)){
            $description=' | DESC: '.$request->description;
        }

        $account_from = Account::find($from_id);
        $account_to = Account::find($to_id);

        # we need to add 2 transactions,
        # Debit the amount from 'From' account, Credit that amoun to 'To' Account

        # dr from 'From' Account
        $transaction_from = AccountGateway::add_transaction([
            'account_id'=>$from_id,
            'type'=>'dr',
            'title'=>'Sent Transfer',
            'description'=>'To Account: '.$account_to->title .'' .$description,
            'tag'=>'transfer',
            'amount'=>$amount,
            'date'=>$transfer_date,
            'links'=>[
                /* It is linked to no one because it is the base entry */
            ]
        ]);

        # cr from 'To' Account
        $transaction_to = AccountGateway::add_transaction([
            'account_id'=>$to_id,
            'type'=>'cr',
            'title'=>'Recieve Transfer',
            'description'=>'From Account: '.$account_from->title .'' .$description,
            'tag'=>'transfer',
            'amount'=>$amount,
            'date'=>$transfer_date,
            'links'=>[
                /* Link transaction_to to transaction_from */
                [
                    'modal'=>get_class(new Account_transaction),
                    'id'=>$transaction_from->id,
                    'tag'=>'transaction'
                ]
            ]
        ]);

        /* Link transaction_from to transaction_to */
        $account_relation = new Account_relation;
        $account_relation->transaction_id=$transaction_from->id;
        $account_relation->tag='transaction';
        $account_relation->subject_type=get_class(new Account_transaction);
        $account_relation->subject_id=$transaction_to->id;
        $account_relation->save();


        return response()->json([
            'account_from'=>[
                'title'=>$account_from->title,
                'department'=>$account_from->department
            ],
            'account_to'=>[
                'title'=>$account_to->title,
                'department'=>$account_to->department
            ],
            'amount'=>$amount,
        ]);
    }

    /**
     * Used to update selectors
     *
    */
    public function FetchAccounts()
    {


        $allow_selection=true;

        $accounts = AccountGateway::getGrantedAccounts();

        # need to check if user has access to accounts
        if(count($accounts)>0){
            return response()->json([
                'status'=>1,
                'accounts'=>$accounts,
            ]);
        }
        else{
            # No account was found, it is in 2 cases
            # 1) logged in user have no right to select account and logged in user have no accounts
            # 2) there is no accounts at all --which is not right
            return response()->json([
                'status'=>0,
                'msg'=>'No account found.',
            ]);
        }


    }

    public function ViewPayablesAccountTransactions()
    {
        return view('Accounts.transactions.pending', ['state' => 'dr']);
    }

    public function ViewReceivableAccountTransactions()
    {
        return view('Accounts.transactions.pending', ['state' => 'cr']);
    }

    public function showPayPendingAccountTransactionForm(int $id, Request $request)
    {
        $state = $request->get('state', 'dr');
        $transaction = Account_transaction::findOrFail($id);
        return view('Accounts.transactions.pay_pending', compact('state', 'transaction'));
    }


    public function PayPendingAccountTransaction(int $transaction_id, Request $request)
    {
        $transaction = Account_transaction::findOrFail($transaction_id);

        $state = $request->get('state', 'dr');

        if($state === 'cr'){

            $request->validate([
                'account_id' => 'required|max:255',
                'date' => 'required|date',
                'amount' => 'required|gt:0',
            ]);

            $account = Account::findOrFail($request->get('account_id'));

        }
        else{

            $account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();

            $request->validate([
                'date' => 'required|date',
                'amount' => 'required|gt:0',
            ]);
        }


        $date = Carbon::parse($request->get('date'))->format('Y-m-d');
        $amount = (float) $request->get('amount', 0);

        # Find ledger against it
        $ledgerIds = $transaction
        ->links()
        ->where('tag', 'ledger')
        ->select('subject_id')
        ->get()
        ->pluck('subject_id')
        ->unique()
        ->toArray();

        Ledger::whereIn('id', $ledgerIds)->where('is_cash', false)->update([
            'is_cash' => true,
            'date' => $date,
            'amount' => $amount,
            'props.account' => [
                'id'=>$account->_id,
                'title'=>$account->title
            ]
        ]);

        $localUtcOffset = request()->cookie('localUtcOffset');
        if (!isset($localUtcOffset)) $localUtcOffset = 000;
        $time = Carbon::parse($date . ' ' . Carbon::now()->format('H:i:s'))->utcOffset($localUtcOffset)->toAtomString();


        $transaction->status = 'paid';
        $transaction->amount = $amount;
        $transaction->account_id = $account->id;
        $transaction->time = $time;
        $transaction->update();
        return response()->json($transaction, 201);
    }

    public function ViewPendingAccountTransactionsData(Request $request)
    {
        $state = $request->get('state', 'dr');
        $paid_cheques = $request->get('paid_cheques', 0) == 1;
        $pending_installments = $request->get('pending_installments', 0) == 1;

        #Find Account Transactions
        $transactions = Account_transaction::with([
            'account' => function($query) {
                $query->select('title');
            },
            'user'
        ])
        ->where('type', $state)
        ->where(function($query) use($paid_cheques) {
            if($paid_cheques){
                $query->where(function($query) {
                    $query->where('status', 'pending')
                        ->orWhere(function($q){
                            $q->where('additional_details.is_cheque', true)
                                ->where('status', 'paid');
                        });
                });
            }else{
                $query->where('status', 'pending');
            }
        })
        ->get()
        ->collect();

        // return get_class($transactions[0]);

        if($pending_installments){
            # ----------------------------------------
            # Fetch pending installments and merge
            #  them with account transaction
            # ----------------------------------------
            $all_installments = Installment::with([
                'user' => function($query){
                    $query->select('name');
                },
                'account' => function($query){
                    $query->select('title');
                }
            ])
            // ->whereNull('transaction_ledger_id')
            ->get();

            # Group by each import
            # means they belongs to single sheet
            $installments = $all_installments
            # Only pending installments
            ->filter(function($item){
                return !isset($item->transaction_ledger_id);
            })
            ->groupBy('code')
            ->flatMap(function($code_items, $code){

                # Group by pay date - each entry that will be charged
                return $code_items->groupBy('pay_date')
                ->map(function($pay_items, $pay_date) use ($code){

                    $item = (object)[];

                    $item->code = $code;
                    $item->date = Carbon::parse($pay_date)->toIso8601String();

                    $item->count = $pay_items->count();
                    $item->pay_amount = $pay_items->sum('pay_amount');
                    $item->charge_amount = $pay_items->sum('charge_amount');

                    # Extra details
                    $first_item = $pay_items->first();
                    $item->_id = $first_item->_id;
                    $item->id = $first_item->id;

                    $item->account_id = $first_item->account_id;
                    $item->account = $first_item->account;

                    $item->by = $first_item->by;
                    $item->user = $first_item->user;

                    $item->charge_date = Carbon::parse($first_item->charge_date)->toIso8601String();

                    $item->updated_at = $first_item->updated_at;
                    $item->created_at = $first_item->created_at;

                    return $item;
                })
                # Since it grouped, remove the keys and convert assoc. array to indexed array
                ->values();
            })
            # Since it grouped, remove the keys and convert assoc. array to indexed array
            ->values();

            # ---------------------------------------------
            # Re-Map & merge them with account transactions
            # ---------------------------------------------

            $installments = $installments->map(function($installment) use ($all_installments){

                return (object)[
                    '_id' => $installment->_id,
                    'account_id' => $installment->account_id,
                    'account' => $installment->account,
                    'user' => $installment->user,
                    'type' => 'dr',
                    'time' => $installment->date,
                    'title' => "Installments | Count: $installment->count",
                    'description' => "
                        Paid: $installment->pay_amount
                        Charged: $installment->charge_amount
                    ",
                    "tag" => "transaction_ledger",
                    "status" => "pending",
                    "amount" => null,
                    "real_amount" => $installment->pay_amount,
                    "transaction_by" => $installment->by,
                    "additional_details" => [
                        "is_cheque" => true,
                        "pending_installment" => true, // RUNTIME data - to diffrenciate between account entry and installment entry
                        "installment_code" => $installment->code,
                        "installment_number" => null,
                        "charge_date" => $installment->charge_date
                    ],
                    "id" => 1,
                    "updated_at" => $installment->updated_at,
                    "created_at" => $installment->created_at,

                ];
            })
            ->values()
            ->all();

            $transactions = $transactions
            # Map installments numbers into installment records
            ->map(function($item) use ($all_installments){

                // Check if installment record - append number into it
                if(str_contains(strtolower($item->title), "installment") && isset($item->additional_details) && isset($item->additional_details['tl_id'])){
                    $transaction_ledger_id = $item->additional_details['tl_id'];

                    $installment = $all_installments->where('transaction_ledger_id', $transaction_ledger_id)
                    # First item because all items belongs to single transaction
                    ->first();

                    if(isset($installment)){
                        $additional_details = $item->additional_details;
                        $additional_details['installment_number'] = $installment->number;
                        $additional_details['installment_code'] = $installment->code;
                        $item->additional_details = $additional_details;
                    }
                }

                return $item;

            })
            ->merge( $installments );
            // return $installments;
        }



        return Datatables::of($transactions)
        ->addColumn('actions', function($transaction){
            return [
                'status'=>1,
            ];
        })
        ->addColumn('dt_created_at', function($transaction){
            $causer= $transaction->user;
            $causer_name = 'No User';
            if (isset($causer)) {
                $causer_name = $causer->name;
            }

            # get local utc offset, so we can show transaction time according to user
            $utc_offset = request()->cookie('kra_utcoffset');
            if(!isset($utc_offset))$utc_offset=250; # default to dubai

            return Carbon::parse($transaction->time)->utcOffset($utc_offset)->format('d/M/Y h:i A').' - '.$causer_name;

        })
        ->addColumn('dt_details', function($transaction){
            $prefix = '';

            if($transaction->tag=="external"){
                # Transaction if from out of system, show transction ID and system
                if(isset($transaction->additional_details)){
                    $origin = isset($transaction->additional_details["origin"]) ? $transaction->additional_details['origin'] : "ds";
                    $transaction_id = $transaction->additional_details['transaction_id'];

                    $originTitle = $origin === "ds" ? "Delivery System"
                    : "";

                    $prefix = '
                    <span class="svg-icon kt-portlet__head-icon d-block transaction__prefix">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15px" height="15px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M3.52270623,14.028695 C2.82576459,13.3275941 2.82576459,12.19529 3.52270623,11.4941891 L11.6127629,3.54050571 C11.9489429,3.20999263 12.401513,3.0247814 12.8729533,3.0247814 L19.3274172,3.0247814 C20.3201611,3.0247814 21.124939,3.82955935 21.124939,4.82230326 L21.124939,11.2583059 C21.124939,11.7406659 20.9310733,12.2027862 20.5869271,12.5407722 L12.5103155,20.4728108 C12.1731575,20.8103442 11.7156477,21 11.2385688,21 C10.7614899,21 10.3039801,20.8103442 9.9668221,20.4728108 L3.52270623,14.028695 Z M16.9307214,9.01652093 C17.9234653,9.01652093 18.7282432,8.21174298 18.7282432,7.21899907 C18.7282432,6.22625516 17.9234653,5.42147721 16.9307214,5.42147721 C15.9379775,5.42147721 15.1331995,6.22625516 15.1331995,7.21899907 C15.1331995,8.21174298 15.9379775,9.01652093 16.9307214,9.01652093 Z" fill="#000000" fill-rule="nonzero" opacity="0.6"/>
                                </g>
                            </svg>
                            <strong>#'.$transaction_id.'</strong> on '.$originTitle.'
                        </span>
                    </span> ';
                }
            }

            if($transaction->tag=="statementledger_transaction"){
                # This is vehicle/booking statement, show booking/vehicle ID and attachment (if found)
                if(isset($transaction->additional_details)){
                    $type = isset($transaction->additional_details["type"]) ? $transaction->additional_details['type'] : null;
                    if(isset($type)){

                        $resource_id = $type === "booking" ? "<a href='".route('tenant.admin.bookings.single.view', $transaction->additional_details['vehicle_booking_id'])."'>#".$transaction->additional_details['vehicle_booking_id']."</a>" : "#".$transaction->additional_details['vehicle_id'];

                        $prefix = '
                        <span class="d-block transaction__prefix m-0">
                            <strong>
                                '.$resource_id.'
                            </strong>
                        </span> ';

                    }
                }
            }



            $desc = $transaction->description;
            if(!isset($desc) || $desc==''){
                # return only title
                return $prefix . $transaction->title;
            }

            # return title along with detailed description
            return $prefix . '<span class="transaction__desc-title">'.$transaction->title.'</span><pre class="transaction__desc-subtitle">'.$desc.'</pre>';
        })
        ->addColumn('dt_cr', function($transaction){
            if($transaction->type=='cr')return $transaction->amount ?? $transaction->real_amount;
            return 0;
        })
        ->addColumn('dt_dr', function($transaction){
            if($transaction->type=='dr')return $transaction->amount ?? $transaction->real_amount;
            return 0;
        })
        ->rawColumns(['dt_details'])
        ->removeColumn('account_id')
        ->removeColumn('type')
        ->make(true);
    }
    public function showEditPendingAccountTransactions(int $id, Request $request)
    {
        $transaction = Account_transaction::findOrFail($id);
        $cheque_beneficiary = Account_transaction::select('additional_details.cheque_beneficiary')
        ->get()
        ->keyBy('additional_details.cheque_beneficiary')
        ->keys();
        return view('Accounts.transactions.edit_pending', compact('transaction', 'id', 'cheque_beneficiary'));
    }
    public function editPendingAccountTransactionsAction(int $id, Request $request)
    {
        $transaction = Account_transaction::findOrFail($id);
        $request->validate([
            'account_id' => 'required|max:255',
            'cheque_beneficiary' => 'required|max:255',
            'date' => 'required|date',
            'cheque_number' => 'nullable|max:255',
        ]);

        $date = Carbon::parse($transaction->time)->setDateFrom(Carbon::parse($request->date))->toIso8601String();

        $additional_details = $transaction->additional_details;
        if($request->filled('cheque_number')) {
            $additional_details['cheque_number'] = $request->cheque_number;
        }
        $additional_details['cheque_beneficiary'] = $request->cheque_beneficiary;
        $transaction->account_id = $request->account_id;
        $transaction->time = $date;
        $additional_details['charge_date'] = $date;
        $transaction->additional_details = $additional_details;
        return $transaction->save() ? response()->json(['message' => 'transaction Saved Successfully', 'status' => 1]) : response()->json(['message' => 'Failed to save transaction', 'status' => 0]);
    }



}
