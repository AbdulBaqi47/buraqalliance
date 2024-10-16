<?php

namespace App\Accounts\Handlers;
use Auth;
use App\Accounts\Handlers\TransactionHandler;
use App\Accounts\Models\Account;
use Illuminate\Validation\ValidationException;

/*
| ----------------------------------------------------
|   Responsible for creating/fetching transactions.
| ----------------------------------------------------
|
|
|
|
*/
class AccountGateway extends TransactionHandler
{

    /**
     * create transactions from here
     * @payload
     *      type: 'cr' or 'dr'
     *      title: title of transaction (a short detail)
     *      description: detailed description of transaction
     *      tag: a reference of transaction like (invoice, loan, transfer, expense)
     *      amount: amount of transaction
     *      links: any links to transaction, required format:
     *              [
     *                  [
     *                      'modal'=>'App\Models\Tenant\Invoice',
     *                      'id'=>1,
     *                      'tag'=>'invoice',
     *                      'extra'=> (any json here)
     *                  ],
     *                  []
     *                  []
     *                  []
     *              ]
    */
    public static function add_transaction($payload)
    {

        /*
        |--------------------------------------------------------------------------
        |   Validating account_id (gets from cookie)
        |--------------------------------------------------------------------------
        */
        if(!isset($payload['account_id'])){
            # If account id is not already found in payload, we need to to fetch account from cookie

            $account_id = request()->cookie('kr_account_selected');
            if(!isset($account_id)){
                #account id not found, throw exeption
                abort(500, 'The cookie "kr_account_selected" is required for handling accounts (this cookie has the id of selected account).');
            }

            $payload['account_id']=$account_id;
        }

        /*
        |--------------------------------------------------------------------------
        | converting to object
        |--------------------------------------------------------------------------
        */
        $payload = (object)$payload;

        /*
        |--------------------------------------------------------------------------
        |   Validation of payload
        |--------------------------------------------------------------------------
        */
        if(!isset($payload->type)){
            abort(500, 'Transaction Type ("cr" or "dr") is required.');
        }
        if(!isset($payload->title)){
            abort(500, 'Transaction title is required.');
        }
        if(!isset($payload->tag)){
            abort(500, 'Transaction Tag is required.');
        }
        if(!isset($payload->status) || $payload->status === "paid" ){

            if(!isset($payload->amount)){
                abort(500, 'Transaction amount is required.');
            }
            if($payload->amount==0){
                abort(500, 'Amount must be greater than zero.');
            }

        }

        /*
        |--------------------------------------------------------------------------
        | It seems all data is corrent, proceed with transaction creating
        |--------------------------------------------------------------------------
        */

        # call to base function to proceed the transaction
        return parent::create_transaction($payload);
    }

    /**
     * Attach links to a transaction
     * @payload
     *      'modal_class'=>'App\Models\Tenant\Invoice',
     *      'modal_id'=>1,
     *      'tag'=>'invoice',
     *      'transaction_id'=> {ID of account transaction entry}
    */
    public static function attach_links_to_transaction($payload)
    {

        /*
        |--------------------------------------------------------------------------
        | converting to object
        |--------------------------------------------------------------------------
        */
        $payload = (object)$payload;

        /*
        |--------------------------------------------------------------------------
        |   Validation of payload
        |--------------------------------------------------------------------------
        */
        if(!isset($payload->transaction_id)){
            abort(500, 'Transaction ID is required.');
        }
        if(!isset($payload->modal_class)){
            abort(500, 'Transaction link class name is required.');
        }
        if(!isset($payload->modal_id)){
            abort(500, 'Transaction link class id is required.');
        }
        if(!isset($payload->tag)){
            abort(500, 'Transaction Tag is required.');
        }

        /*
        |--------------------------------------------------------------------------
        | It seems all data is corrent, proceed with transaction creating
        |--------------------------------------------------------------------------
        */

        # call to base function to proceed the transaction
        return parent::attach_links_to_transaction($payload);
    }

    /**
     * edit transactions from here
     * @payload
     *  *Same as add_transaction method*
     *  transaction_id: id of what transaction you need to edit
    */
    public static function edit_transaction($payload)
    {
        /*
        |--------------------------------------------------------------------------
        |   Validating account_id (gets from cookie)
        |--------------------------------------------------------------------------
        */
        if(!isset($payload['account_id'])){
            # If account id is not already found in payload, we need to to fetch account from cookie

            $account_id = request()->cookie('kr_account_selected');
            if(!isset($account_id)){
                #account id not found, throw exeption
                abort(500, 'The cookie "kr_account_selected" is required for handling accounts (this cookie has the id of selected account).');
            }

            $payload['account_id']=$account_id;
        }


        /*
        |--------------------------------------------------------------------------
        | converting to object
        |--------------------------------------------------------------------------
        */
        $payload = (object)$payload;

        /*
        |--------------------------------------------------------------------------
        |   Validation of payload
        |--------------------------------------------------------------------------
        */
        if(!isset($payload->transaction_id)){
            abort(500, 'Transaction id is required.');
        }
        if(!isset($payload->type)){
            abort(500, 'Transaction Type ("cr" or "dr") is required.');
        }
        if(!isset($payload->title)){
            abort(500, 'Transaction title is required.');
        }
        if(!isset($payload->tag)){
            abort(500, 'Transaction Tag is required.');
        }
        if(!isset($payload->amount)){
            abort(500, 'Transaction amount is required.');
        }
        if($payload->amount==0){
            abort(500, 'Amount must be greater than zero.');
        }

        /*
        |--------------------------------------------------------------------------
        | It seems all data is corrent, proceed with transaction creating
        |--------------------------------------------------------------------------
        */

        # call to base function to proceed the transaction
        return parent::edit_transaction($payload);
    }

    public static function edit_transaction_($payload)
    {
        /*
        |--------------------------------------------------------------------------
        |   Validating account_id (gets from cookie)
        |--------------------------------------------------------------------------
        */



        /*
        |--------------------------------------------------------------------------
        | converting to object
        |--------------------------------------------------------------------------
        */
        $payload = (object)$payload;

        /*
        |--------------------------------------------------------------------------
        |   Validation of payload
        |--------------------------------------------------------------------------
        */

        if($payload->amount==0){
            abort(500, 'Amount must be greater than zero.');
        }

        /*
        |--------------------------------------------------------------------------
        | It seems all data is corrent, proceed with transaction creating
        |--------------------------------------------------------------------------
        */

        # call to base function to proceed the transaction
        return parent::edit_transaction_($payload);
    }

    public static function deleteTransaction__($payload)
    {

        /*
        |--------------------------------------------------------------------------
        | converting to object
        |--------------------------------------------------------------------------
        */
        $payload = (object)$payload;

        /*
        |--------------------------------------------------------------------------
        |   Validation of payload
        |--------------------------------------------------------------------------
        */
        if(!isset($payload->transaction_id)){
            abort(500, 'Transaction id is required.');
        }

        /*
        |--------------------------------------------------------------------------
        | It seems all data is corrent, proceed with transaction creating
        |--------------------------------------------------------------------------
        */

        # call to base function to proceed the transaction
        return parent::deleteTransaction_($payload);
    }

    /**
     * will find the transaction by searching on "account_relations" table
     * @payload
     *  tag: like 'invoice'
     *  id: id of relation, like id of invoice
    */
    public static function findByLink($tag, $id)
    {

        $link = parent::find_transaction((object)[
            'by'=>'link',
            'tag'=>$tag,
            'id'=>$id,
        ]);

        return $link;
    }

    /**
     * will delete the transaction by searching on "account_relations" table
     * @payload
     *  tag: like 'invoice'
     *  id: id of relation, like id of invoice
    */
    public static function deleteByLink($tag, $id)
    {

        $link = parent::find_transaction((object)[
            'by'=>'link',
            'tag'=>$tag,
            'id'=>$id,
        ]);

        if(isset($link)){
            #delete the transaction
            $transaction_id = $link->transaction_id;
            parent::delete_transaction($transaction_id);

            return $transaction_id;
        }
        return null;
    }


    /**
     * will Validate cookie
    */
    public static function validateCookie()
    {
        /*
        |--------------------------------------------------------------------------
        |   Validating account_id (gets from cookie)
        |--------------------------------------------------------------------------
        */
        $account_id = request()->cookie('kr_account_selected');
        if(!isset($account_id)){
            #account id not found, throw exeption
            throw ValidationException::withMessages(['account' => 'Account is required for this entry']);
        }

        return $account_id;
    }

    /**
     * will Validate balance
    */
    public static function validateBalance($input_name = 'amount')
    {
        $account_id=self::validateCookie();
        $account = self::getAccount($account_id);

        $balance = self::getAccountBalance($account);

        $amount = request()->get($input_name, null);
        if(!isset($amount)){
            # Input not found?
            return true;
        }

        $amount = (float)$amount;

        $valid = $amount <= $balance;

        # -----------------------------
        # Negative Balance Access Check
        # -----------------------------
        if(app('helper_service')->routes->has_custom_access('negative_account_balance', [$account_id])){
            $valid = true;
        }

        if(!$valid){
            throw ValidationException::withMessages(['Balance' => 'Insufficient balance in '.$account->title]);
        }

        return $valid;
    }

    /**
     * will return the selected account from cookie
    */
    public static function getSelectedAccount()
    {
        /*
        |--------------------------------------------------------------------------
        |   Validating account_id (gets from cookie)
        |--------------------------------------------------------------------------
        */
        $account_id=self::validateCookie();


        $account = Account::find($account_id);

        if(!isset($account)){
            throw ValidationException::withMessages(['account' => 'Account not found']);
        }

        return $account;
    }

    /**
     * Used to get the account by id
    */
    public static function getAccount($account_id)
    {
        $account = Account::find($account_id);

        if(!isset($account)){
            throw ValidationException::withMessages(['account' => 'Account not found']);
        }

        return $account;
    }

    /**
     * Used to get the granted/all accounts of user
    */
    private static function getAccounts($all=false)
    {
        $user = Auth::user();
        $all_accounts = false;
        if(isset($user->props)&&isset($user->props['all_accounts']))$all_accounts = $user->props['all_accounts'];

        if($user->type=='su')$all_accounts=true;

        # If all accounts requested || User have all account access
        if($all||$all_accounts){
            # Return all the accounts
            return Account::all();
        }

        # Return only granted accounts

        # Find accounts granted to this user
        $granted_accounts = $user->accounts;
        return $granted_accounts;

    }

    /**
     * Used to get the all accounts of user
    */
    public static function getAllAccounts()
    {
        return self::getAccounts(true);
    }

    /**
     * Used to get the granted accounts of user
    */
    public static function getGrantedAccounts()
    {
        return self::getAccounts();
    }

    /**
     * Used to get the granted accounts of user
    */
    public static function getAccountsOf( $department, $type )
    {
        if(isset($department) && isset($type)){
            return Account::where('department', $department)
            ->where('type', $type)
            ->get();
        }
        return collect([]);
    }



    /**
     * Used to calculate the balance (Called in Account.php model)
    */
    public static function getAccountBalance($account)
    {

        $balance=0;
        # --------------------------------
        #       MONGODB aggregate
        # --------------------------------
        $account_id = $account->_id;
        $agg = \App\Accounts\Models\Account_transaction::raw(function($collection) use ($account_id){
            return $collection->aggregate([
                [
                    '$match'=> [
                        "account_id"=>"$account_id",
                        "status"=>"paid",
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$group'=> [
                        "_id"=> null,
                        "balance"=> [
                            '$sum'=> [
                                '$cond'=> [
                                    [ '$eq'=> [ '$type', 'dr' ] ],
                                    [ '$subtract' => [ 0, '$amount' ] ],
                                    '$amount'

                                ]
                            ]
                         ]
                    ]
                ]
            ]);
        })->first();

        if(isset($agg))$balance=$agg->balance;


        return round($balance, 2);
    }
    public static function deleteTransaction($payload){
        $payload = (object)$payload;

        return parent::deleteTransaction($payload);


    }
}
