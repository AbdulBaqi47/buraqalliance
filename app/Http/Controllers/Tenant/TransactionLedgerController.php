<?php

namespace App\Http\Controllers\Tenant;

use App\Accounts\Models\Account_transaction;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Addon;
use App\Models\Tenant\AddonDeduction;
use App\Models\Tenant\AddonExpense;
use App\Models\Tenant\AddonsSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Tenant\Driver;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionLedgerController extends Controller
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


    public function showBreakDownView(Request $request, int $ledger_id)
    {
        $ledger = Ledger::findOrFail($ledger_id);

        $model = $ledger->source_model;
        $ledger->source = $model::with([
            'chargeables' => function($query){
                $query->select('id', 'tl_id', 'amount', 'source_model', 'source_id', 'description');
            },
            'payables' => function($query){
                $query->select('id', 'account_id', 'amount', 'status', 'additional_details');
            },
            'payables.account' => function($query){
                $query->select('id', 'title');
            },
        ])
        ->findOrFail($ledger->source_id);


        $total_payables = $ledger->source->payables->sum('amount');
        $total_chargeables = $ledger->source->chargeables->sum('amount');

        return view('Tenant.tl.breakdown',compact('ledger', 'total_payables', 'total_chargeables'));
    }

    public function editBreakdownPayable(Request $request, int $transaction_id)
    {
        $request->validate([
            'amount' => 'required|gt:0'
        ]);

        $amount = (float) $request->get('amount');

        $transaction = Account_transaction::findOrFail($transaction_id);

        if(!isset($transaction->amount)){
            # Actual amount is not set yet, update the real amount
            $transaction->real_amount = $amount;
        }
        else{
            $transaction->amount = $amount;
        }

        $transaction->save();

        return response()->json([
            'status' => 1
        ]);

    }

    public function deleteBreakdownPayable(Request $request, int $transaction_id)
    {

        $transaction = Account_transaction::findOrFail($transaction_id);

        $transaction->delete();

        return response()->json([
            'status' => 1
        ]);

    }

}
