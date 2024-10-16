<?php

namespace App\Http\Controllers\API;

use App\Accounts\Handlers\AccountGateway;
use App\Accounts\Models\Account;
use App\Accounts\Models\Account_transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * Add trancsaction to main account [just to sync account, this entry may add in other system like Delivery System]
    */
    public function addTransaction(Request $request)
    {
        $origin = $request->has('_origin') ? $request->get('_origin') : "ds"; # Origin of request, default is kingrider delivery system

        # Get "main" "bank" account
        $account = Account::where('department', 'bank')
        ->where('type', "main")
        ->get()
        ->first();
        if(!isset($account)){
            return response()->json([
                "message" => "Main Account not found"
            ], 404);
        }

        switch ($origin) {
            case 'ds':
                $amount = $request->get('amount');
                $type = $request->get('type');
                $transaction_id = $request->get('id');
                $transaction_by = $request->get('transaction_by');
                $date = $request->get('date');
                $tag = $request->get('tag');
                $detail = $request->get('detail');

                $transaction = AccountGateway::add_transaction([
                    'account_id'=>$account->_id,
                    'type'=>$type,
                    'date'=>$date,
                    'title'=>'External Transaction - by Delivery System',
                    'description'=>$detail,
                    'tag'=>'external',
                    'amount'=>$amount,
                    'links'=>[ ],
                    'additional_details' => [
                        "origin" => $origin,
                        "tag" => $tag,
                        "transaction_id" => $transaction_id,
                        "transaction_by" => $transaction_by
                    ]
                ]);

                return response()->json($transaction);

                break;

            default:
                # code...
                break;
        }
        return $request->all();
    }
     public function deleteTransaction(Request $request)
     {
        $origin = $request->has('_origin') ? $request->get('_origin') : "ds"; # Origin of request, default is kingrider delivery system
        $transaction_id = $request->get('id');

        # Get "main" "bank" account
        $transaction = Account_transaction::where('additional_details.transaction_id',$transaction_id)->get()->first();
        if (!isset($transaction)) {
            return response()->json([
                'status' => $transaction_id,
                'msg' => 'No transaction found',
            ]);
        }

        switch ($origin) {
            case 'ds':
                $transaction_id = $request->get('id');

                $transaction = AccountGateway::deleteTransaction__([
                    "transaction_id" => $transaction_id
                ]);

                return response()->json($transaction);

                break;

            default:
                # code...
                break;
        }
        return $request->all();
    }
}
