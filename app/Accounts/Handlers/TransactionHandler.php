<?php

namespace App\Accounts\Handlers;

use Auth;

use Carbon\Carbon;

use App\Accounts\Models\Account_transaction;
use App\Accounts\Models\Account;
use App\Accounts\Models\Account_relation;
use App\Models\Tenant\BackgroundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/*
| ----------------------------------------------------
|   Responsible for All transactions operations.
| ----------------------------------------------------
|
|
|
|
*/

class TransactionHandler
{

    /**
     * create transactions from here
     * @payload
     *      account_id: id of account of transaction
     *      tag: a reference of transaction like (invoice, loan, transfer, expense)
     *      amount: amount of transaction
     */
    protected static function create_transaction($payload)
    {

        #payload
        $account_id = $payload->account_id;
        $type = $payload->type;
        $title = $payload->title;
        $date = isset($payload->date) ? $payload->date : Carbon::now()->format('Y-m-d');
        $description = isset($payload->description) ? $payload->description : null;
        $type = $payload->type;
        $tag = $payload->tag;
        $amount = $payload->amount ?? null;
        $links = $payload->links;
        $status = $payload->status ?? 'paid';
        $transaction_by = $payload->transaction_by ?? null;

        # The amount that was generated / calulated
        # This amount may differ than $amount, since $amount is what was received / paid
        $real_amount = $payload->real_amount ?? null;

        $localUtcOffset = request()->cookie('localUtcOffset');
        if (!isset($localUtcOffset)) $localUtcOffset = 000;
        $time = Carbon::parse($date . ' ' . Carbon::now()->format('H:i:s'))->utcOffset($localUtcOffset)->toAtomString();


        #adding transaction
        $transaction = new Account_transaction;
        $transaction->account_id = $account_id;
        $transaction->type = $type;
        $transaction->time = $time;
        $transaction->title = $title;
        $transaction->description = $description;
        $transaction->tag = $tag;
        $transaction->status = $status;
        $transaction->amount = isset($amount) ? round($amount, 2) : null;
        $transaction->real_amount = isset($real_amount) && $real_amount !== '' && $real_amount > 0 ? round($real_amount, 2) : null;
        $transaction->transaction_by = isset($transaction_by) ? $transaction_by : ( Auth::check() ? Auth::user()->id : null );
        if (isset($payload->additional_details)) $transaction->additional_details = $payload->additional_details;
        $transaction->save();


        #adding links
        foreach ($links as $link) {
            $account_relation = new Account_relation;
            $account_relation->transaction_id = $transaction->id;
            $account_relation->tag = $link['tag'];
            $account_relation->subject_type = $link['modal'];
            $account_relation->subject_id = $link['id'];
            $account_relation->save();
        }

        // if ($transaction->tag !== "external") { # Exclude external transactions
        //     $account = Account::find($account_id);
        //     if ($account->department === "bank" && $account->type === "main") { # Sync only main account entries

        //         /*
        //         |----------------------------------------------------
        //         | Sending API request to DS to sync the transaction
        //         |----------------------------------------------------
        //         */

        //         $record = [];
        //         foreach ($transaction->toArray() as $key => $value) {
        //             $record[$key] = $value;
        //         }
        //         $record["_origin"] = "ws"; # origin of request, will help remote app to do static things

        //         $response = Http::post(config('kingriders.ds_url').'/api/workshop/transaction/add?token='.config('kingriders.apikey'), $record);

        //         if (!$response->ok()) {
        //             $backgroundException = new BackgroundException;
        //             $backgroundException->title = "TransactionHandler:create_transaction with status '" . $response->status() . "' on transaction #" . $transaction->_id;
        //             $backgroundException->details = $response->body();
        //             $backgroundException->save();
        //         }

        //         Log::channel('sync-transaction')->info(config('kingriders.ds_url') . '/api/workshop/transaction/add?token=' . config('kingriders.apikey'));
        //         Log::channel('sync-transaction')->info($response->status());
        //         Log::channel('sync-transaction')->info(print_r($response->body(), true));
        //     }
        // }
        return $transaction;
    }

    # attach links to the transaction
    protected static function attach_links_to_transaction($payload)
    {
        $transaction_id = $payload->transaction_id;
        $modal_class = $payload->modal_class;
        $modal_id = $payload->modal_id;
        $tag = $payload->tag;

        $account_relation = new Account_relation;
        $account_relation->transaction_id = $transaction_id;
        $account_relation->tag = $tag;
        $account_relation->subject_type = $modal_class;
        $account_relation->subject_id = $modal_id;
        $account_relation->save();

        return $account_relation;

    }

    # edit the transaction
    protected static function edit_transaction($payload)
    {
        #payload
        $account_id = $payload->account_id;
        $transaction_id = $payload->transaction_id;
        $type = $payload->type;
        $title = $payload->title;
        $description = isset($payload->description) ? $payload->description : null;
        $type = $payload->type;
        $tag = $payload->tag;
        $amount = $payload->amount;
        if (isset($payload->additional_details)) $additional_details =$payload->additional_details;
        $links = isset($payload->links) ? $payload->links : [];


        #adding transaction
        $transaction = Account_transaction::find($transaction_id);
        if (!isset($transaction)) abort(500, 'No transaction found against this id.');

        $transaction->account_id = $account_id;
        $transaction->type = $type;
        if(isset($payload->status)){
            $transaction->status = $payload->status;
        }
        $transaction->title = $title;
        $transaction->description = $description;
        $transaction->tag = $tag;
        $transaction->amount = round($amount, 2);
        $transaction->additional_details = $additional_details;
        // $transaction->transaction_by = Auth::user()->id;
        $transaction->save();


        #adding links
        foreach ($links as $link) {

            # check if this found on already added links
            $link_found = $transaction->links()
                ->where('subject_type', $link['modal'])
                ->where('subject_id', $link['id'])
                ->first();
            if (!isset($link_found)) {
                $account_relation = new Account_relation;
                $account_relation->transaction_id = $transaction->id;
                $account_relation->tag = $link['tag'];
                $account_relation->subject_type = $link['modal'];
                $account_relation->subject_id = $link['id'];
                $account_relation->save();
            }
        }
        // if ($transaction->tag !== "external") { # Exclude external transactions
        //     $account = Account::find($account_id);
        //     if ($account->department === "bank" && $account->type === "main") { # Sync only main account entries

        //         /*
        //         |----------------------------------------------------
        //         | Sending API request to DS to sync the transaction
        //         |----------------------------------------------------
        //         */

        //         $record = [];
        //         foreach ($transaction->toArray() as $key => $value) {
        //             $record[$key] = $value;
        //         }
        //         $record["_origin"] = "ws"; # origin of request, will help remote app to do static things

        //         $response = Http::post(config('kingriders.ds_url') . '/api/workshop/transaction/edit?token=' . config('kingriders.apikey'), $record);

        //         if (!$response->ok()) {
        //             $backgroundException = new BackgroundException;
        //             $backgroundException->title = "TransactionHandler:create_transaction with status '" . $response->status() . "' on transaction #" . $transaction->_id;
        //             $backgroundException->details = $response->body();
        //             $backgroundException->save();
        //         }

        //         Log::channel('sync-transaction')->info(config('kingriders.ds_url') . '/api/workshop/transaction/edit?token=' . config('kingriders.apikey'));
        //         Log::channel('sync-transaction')->info($response->status());
        //         Log::channel('sync-transaction')->info(print_r($response->body(), true));
        //     }
        // }

        return $transaction;
    }

    # edit the transaction 2
    protected static function edit_transaction_($payload)
    {
        #payload
        $transaction_id =  $payload->transaction_id;
        $tag = $payload->tag;
        $amount = $payload->amount;


        #editing transaction
        $transaction = Account_transaction::find($transaction_id);
        if (!isset($transaction)) abort(500, 'No transaction found against this id.' . $transaction_id);
        $transaction->amount = round($amount, 2);
        $transaction->save();



        // if (isset($transaction->tag) && $transaction->tag !== "external") { # Exclude external transactions
        //     $account = Account::find($transaction->account_id);
        //     if ($account->department === "bank" && $account->type === "main") { # Sync only main account entries

        //         /*
        //         |----------------------------------------------------
        //         | Sending API request to DS to sync the transaction
        //         |----------------------------------------------------
        //         */

        //         $record = [];
        //         foreach ($transaction->toArray() as $key => $value) {
        //             $record[$key] = $value;
        //         }
        //         $record["_origin"] = "ws"; # origin of request, will help remote app to do static things

        //         $response = Http::post(config('kingriders.ds_url') . '/api/workshop/transaction/edit?token=' . config('kingriders.apikey'), $record);

        //         if (!$response->ok()) {
        //             $backgroundException = new BackgroundException;
        //             $backgroundException->title = "TransactionHandler:create_transaction with status '" . $response->status() . "' on transaction #" . $transaction->_id;
        //             $backgroundException->details = $response->body();
        //             $backgroundException->save();
        //         }

        //         Log::channel('sync-transaction')->info(config('kingriders.ds_url') . '/api/workshop/transaction/edit?token=' . config('kingriders.apikey'));
        //         Log::channel('sync-transaction')->info($response->status());
        //         Log::channel('sync-transaction')->info(print_r($response->body(), true));
        //     }
        // }

        return $transaction;
    }


    protected static function find_transaction($payload)
    {
        switch ($payload->by) {
            case 'link':
                return Account_relation::with('transaction')->where('tag', $payload->tag)
                    ->where('subject_id', $payload->id)
                    ->get()
                    ->first();
                break;
            case 'id':
                return Account_transaction::with('links')->find($payload->id);
                break;
        }
    }

    protected static function delete_transaction($transaction_id)
    {
        #check if transaction is exists
        $transaction = Account_transaction::find($transaction_id);
        if (!isset($transaction)) abort(500, 'No transaction found against this id.');

        #delete the links
        $transaction->links()->delete();

        #delete the transaction
        $transaction->delete();
    }
    protected static function deleteTransaction_($payload)
    {
        #check if transaction is exists
        $id = $payload->transaction_id;
        $transaction = Account_transaction::where('additional_details.transaction_id', $id)->get()->first();
        if (!isset($transaction)) abort(500, 'No transaction found against this id.');

        #delete the transaction
        $transaction->delete();
    }

    protected static function deleteTransaction($payload)
    {
        #check if transaction is exists
        $id = $payload->transaction_id;
        $transaction = Account_transaction::find($id);
        if (!isset($transaction)) abort(500, 'No transaction found against this id.');

        #delete the transaction
        $transaction->delete();
        // if ($transaction->tag !== "external") { # Exclude external transactions
        //     $account = Account::find($transaction->account_id);
        //     if ($account->department === "bank" && $account->type === "main") { # Sync only main account entries

        //         /*
        //         |----------------------------------------------------
        //         | Sending API request to DS to sync the transaction
        //         |----------------------------------------------------
        //         */

        //         $record = [];
        //         foreach ($transaction->toArray() as $key => $value) {
        //             $record[$key] = $value;
        //         }
        //         $record["_origin"] = "ws"; # origin of request, will help remote app to do static things

        //         $response = Http::DELETE(config('kingriders.ds_url') . '/api/workshop/transaction/delete?token=' . config('kingriders.apikey'), $record);

        //         if (!$response->ok()) {
        //             $backgroundException = new BackgroundException;
        //             $backgroundException->title = "TransactionHandler:create_transaction with status '" . $response->status() . "' on transaction #" . $transaction->_id;
        //             $backgroundException->details = $response->body();
        //             $backgroundException->save();
        //         }

        //         Log::channel('sync-transaction')->info(config('kingriders.ds_url') . '/api/workshop/transaction/delete?token=' . config('kingriders.apikey'));
        //         Log::channel('sync-transaction')->info($response->status());
        //         Log::channel('sync-transaction')->info(print_r($response->body(), true));
        //     }
        // }
    }
}
