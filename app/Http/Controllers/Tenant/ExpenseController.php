<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Tenant\{Addon, AddonExpense, AddonsSetting, Company_expense,DriverVisaExpense, Driver, Vehicle};
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;

class ExpenseController extends Controller
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
     * Create Expense form
     *
    */
    public function showExpenseForm()
    {
        $types = Company_expense::groupBy('type')->get()
        ->keyBy('type')
        ->keys();

        return view('Tenant.expense.create', compact('types'));
    }

    /**
     * Create Company Expense
     *
    */
    public function create(Request $request)
    {
        # This request involve cash, we better validate accounts
        \App\Accounts\Handlers\AccountGateway::validateCookie();

        $validated = $request->validate([
            'amount' => 'required|gt:0',
            'type' => 'required|max:255',
        ]);

        # Payload
        $month=Carbon::parse($request->month)->format('Y-m-d');
        $given_date=Carbon::parse($request->given_date)->format('Y-m-d');
        $type=$request->type;
        $description=$request->description;
        $amount=(float)$request->amount;
        $has_tax = $request->has('has_tax');
        $invoice_tax_amount=null;
        $invoice_tax_id=null;
        $invoice_tax_img=null;
        if($has_tax){
            $invoice_tax_amount=(float)$request->invoice_tax_amount;
            $invoice_tax_id=(int)$request->invoice_tax_id;
        }

        #need to check if image added
        if($request->hasFile('invoice_tax_img')){
            $filepath = Storage::putfile('expenses', $request->file('invoice_tax_img'));
            $invoice_tax_img = $filepath;
        }

        $user = Auth::user();

        # Create expense
        $expense = new Company_expense;
        $expense->month = $month;
        $expense->given_date = $given_date;
        $expense->type = $type;
        $expense->description = $description;
        $expense->amount = $amount;
        $expense->has_tax = $has_tax;
        $expense->invoice_tax_amount = $invoice_tax_amount;
        $expense->invoice_tax_id = $invoice_tax_id;
        $expense->invoice_tax_img = $invoice_tax_img;
        $expense->save();

        $selected_account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();


        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$expense->_id;
        $ledger->source_model=get_class($expense);
        $ledger->date=$given_date;
        $ledger->month = $month; // For Filteration Purpose
        $ledger->tag="expense";
        $ledger->is_cash=true;
        $ledger->amount=$amount;
        $ledger->props=[
            'by'=>$user->id,
            'account'=>[
                'id'=>$selected_account->_id,
                'title'=>$selected_account->title
            ]
        ];
        $ledger->save();


        #create account transaction
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
            'type'=>'dr',
            'date' => $given_date,
            'title'=>'Company Expense',
            'description'=>$type.' | '.$description.' | '.Carbon::parse($month)->format('M Y'),
            'tag'=>'expense',
            'amount'=>$amount,
            'links'=>[
                [
                    'modal'=>get_class(new Company_expense),
                    'id'=>$expense->id,
                    'tag'=>'expense'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ]
            ]
        ]);

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

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $expense->id;
        $relation->source_model = get_class($expense);
        $relation->tag = 'expense';
        $relation->is_real = true;
        $relation->save();

        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();
        // Converting Ledger To Array And Chnage Time To Required Format As We Cannot Change Format Of Database Datetime Object
        $ledger_obj = $ledger->toArray();
        $ledger_obj['date'] = Carbon::parse($ledger->date)->format('d F, Y');

        # We will return ledger because expense will be added from ledger page
        return response()->json($ledger_obj);
    }

    public function showDriverExpenseForm() {
        $types = DriverVisaExpense::groupBy('type')->get()
        ->keyBy('type')
        ->keys();
        $drivers = Driver::select('id', 'name')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->full_name,
                'selected' => null
            ];
        });
        return view('Tenant.drivers.actions.create_expense', compact('types','drivers'));
    }

    public function saveDriverExpense(Request $request){
        # This request involve cash, we better validate accounts
        \App\Accounts\Handlers\AccountGateway::validateCookie();

        $validated = $request->validate([
            'amount' => 'required|gt:0',
            'type' => 'required|max:255',
            'attachment' => 'required|image',
            'driver_id' => 'required|numeric'
        ]);

        # Payload
        $month=Carbon::parse($request->month)->format('Y-m-d');
        $given_date=Carbon::parse($request->given_date)->format('Y-m-d');
        $type=$request->type;
        $description=$request->description;
        $amount=(float)$request->amount;
        $attachment=null;

        #need to check if image added
        if($request->hasFile('attachment')){
            $filepath = Storage::putfile('expenses', $request->file('attachment'));
            $attachment = $filepath;
        }

        $user = Auth::user();

        # Create expense
        $expense = new DriverVisaExpense;
        $expense->driver_id = intval($request->driver_id);
        $expense->month = $month;
        $expense->given_date = $given_date;
        $expense->type = $type;
        $expense->description = $description;
        $expense->amount = $amount;
        $expense->invoice_tax_img = $attachment;
        $expense->save();

        $selected_account = \App\Accounts\Handlers\AccountGateway::getSelectedAccount();


        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$expense->_id;
        $ledger->source_model=get_class($expense);
        $ledger->date=$given_date;
        $ledger->month = $month; // For Filteration Purpose
        $ledger->tag="driver_visa_expense";
        $ledger->is_cash=true;
        $ledger->amount=$amount;
        $ledger->props=[
            'by'=>$user->id,
            'account'=>[
                'id'=>$selected_account->_id,
                'title'=>$selected_account->title
            ]
        ];
        $ledger->save();


        #create account transaction
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
            'type'=>'dr',
            'title'=>'Driver Visa Expense',
            'description'=>$type.' | '.$description.' | '.Carbon::parse($month)->format('M Y'),
            'tag'=>'expense',
            'amount'=>$amount,
            'links'=>[
                [
                    'modal'=>get_class(new DriverVisaExpense),
                    'id'=>$expense->id,
                    'tag'=>'expense'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ]
            ]
        ]);

        # Modify ledger model so it can load on ledger page
        $ledger->source = $expense;
        $ledger->user = $user;
        $ledger->description = \App\Http\Controllers\Tenant\LedgerController::generate_description($ledger);
        $ledger->cr=0;
        $ledger->dr=$ledger->amount;
        $ledger->account = $selected_account->title;
        $ledger->paid_by = $user->name;
        $ledger->actions = [
            'status'=>1,
        ];
        $ledger->date=Carbon::parse($ledger->date)->format('d F, Y');

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $expense->id;
        $relation->source_model = get_class($expense);
        $relation->tag = 'expense';
        $relation->is_real = true;
        $relation->save();

        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();
        // Converting Ledger To Array And Chnage Time To Required Format As We Cannot Change Format Of Database Datetime Object
        $ledger_obj = $ledger->toArray();
        $ledger_obj['date'] = Carbon::parse($ledger->date)->format('d F, Y');

        # We will return ledger because expense will be added from ledger page
        return response()->json($ledger_obj);
    }
}
