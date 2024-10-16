<?php

namespace App\Http\Controllers\Tenant;

use App\Accounts\Handlers\AccountGateway;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChequeController extends Controller
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
     * Create view Page of cheque addition
     *
    */
    public function showChequeForm($config=null)
    {
        $accounts = AccountGateway::getAccountsOf('bank', 'beneficiary')
        ->map(function($item){
            return [
                'id' => $item->id,
                'text' => $item->title
            ];
        });
        return view('Tenant.cheques.create', compact('config', 'accounts'));
    }

    /**
     * POST request of creating the cheque
     *
    */
    public function create_cheque(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|max:255',
        ]);

        $pay_date =  Carbon::parse($request->date);
        $amount = (float) $request->get('amount');
        $account_id = $request->get('beneficiary_id');
        $account = AccountGateway::getAccount($account_id);
        $isGuarantee = $request->has('guarantee');
        $notes = $request->get('notes', '');

        $by = Auth::user()->id;

        # -----------------------------
        #   Insert in Master Table
        # -----------------------------
        $transactionLedger = new TransactionLedger;
        $transactionLedger->title = "Cheque" . ($isGuarantee ? " - guarantee" : '');
        $transactionLedger->given_date = $pay_date->format('Y-m-d');
        $transactionLedger->month = $pay_date->copy()->startOfMonth()->format('Y-m-d');
        $transactionLedger->amount = $amount;
        $transactionLedger->description = "Beneficiary: $account->title" . (isset($notes) && trim($notes) !== '' ? "<br /> $notes" : '');
        $transactionLedger->by = $by;
        $transactionLedger->tag = "cheque";
        $transactionLedger->save();


        # -------------------------------------------
        #   Insert Ledger - Will show in daily ledger
        # -------------------------------------------
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$transactionLedger->id;
        $ledger->source_model=get_class($transactionLedger);
        $ledger->date=$transactionLedger->given_date;
        $ledger->tag="transaction_ledger";
        $ledger->month = $transactionLedger->month; // For Filteration Purpose
        $ledger->is_cash=true;
        $ledger->amount=$amount;
        $ledger->props=[
            'by'=>$by,
            'account'=>[
                'id'=>$account->_id,
                'title'=>$account->title
            ]
        ];
        $ledger->save();


        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transactionLedger->id;
        $relation->source_model = get_class($transactionLedger);
        $relation->tag = 'transaction_ledger';
        $relation->is_real = true;
        $relation->save();


        # -----------------------------
        #   Insert Account Transaction
        # -----------------------------
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
            'account_id' => $account->id,
            'type'=>"dr",
            'date' => $pay_date->format('Y-m-d'),
            'title'=>$transactionLedger->title,
            'description'=>$transactionLedger->description,
            'tag'=>'transaction_ledger',
            'status' => "pending",
            'transaction_by' => $by,
            'amount'=>$amount,
            'additional_details' => [
                "tl_id" => $transactionLedger->id,
                "is_cheque" => true,
                'added_from_module' => true,
                'is_guarantee' => $isGuarantee,
                'charge_date' => $pay_date->format('Y-m-d')
            ],
            'links'=>[
                [
                    'modal'=>get_class(new TransactionLedger),
                    'id'=>$transactionLedger->id,
                    'tag'=>'transaction_ledger'
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
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();

        return response()->json([
            'status' => 1
        ]);
    }

}
