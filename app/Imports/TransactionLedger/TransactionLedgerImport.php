<?php

namespace App\Imports\TransactionLedger;

use App\Accounts\Models\Account;
use App\Models\Tenant\Addon;
use App\Models\Tenant\AddonDeduction;
use App\Models\Tenant\AddonExpense;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\VehicleLedgerItem;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithEvents;

class TransactionLedgerImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{

    use RemembersChunkOffset;

    private $totalRows;
    private $importedRows;
    private $type;
    private TransactionLedger $transaction_ledger;
    private $ledgers;
    private $parent;

    public function __construct($transaction_ledger, $type, &$ledgers, $parent)
    {
        $this->totalRows = 0;
        $this->importedRows = 0;
        $this->transaction_ledger = $transaction_ledger;

        $this->type = $type;
        $this->ledgers = &$ledgers;
        $this->parent = $parent;
    }

    /**
    * @param \Illuminate\Support\Collection $rows
    *
    * @return null
    */
    public function collection(Collection $rows): void
    {


        if($this->type === "payable"){

            $this->parent->_IH_addData([
                'total_records_payable' => $rows->count()
            ]);

            $this->handlePayable($rows);

        }
        else if($this->type === "chargeable"){

            $this->parent->_IH_addData([
                'total_records_chargeable' => $rows->count()
            ]);

            $this->handleChargeables($rows);

            $this->parent->_IH_end();

        }
    }

    /**
    *  Handle Chargeable records
    *
    * @param \Illuminate\Support\Collection $rows
    *
    * @return null
    */
    private function handleChargeables(Collection $rows) : void
    {

        if(!isset($this->ledgers) || count($this->ledgers) === 0){
            throw ValidationException::withMessages(["1" => "Ledgers are required"]);
            return;
        }

        # Perform basic header validation on 1st row
        $errorFound = $this->headersValidation([
            'source-type',
            'source-id',
            'amount'
        ], $rows->first()->toArray());

        if(!$errorFound){

            $errors = [];

            $rows = $rows->map(function($item){
                $item['amount'] = (float)str_replace(",", "", $item['amount']);
                return $item;
            });


            # ------------------
            #   Insert Records
            # ------------------
            foreach ($rows as $rowIndex => $row) {
                $rowNo = $rowIndex + 2; # +1 beacause index start at zero, +1 because we have header row
                $amount = $row['amount'];
                $sourceType = $row['source-type'];
                $sourceId = $row['source-id'];
                $notes = $row['notes'] ?? null;
                $item_month = $row['month'] ?? null;
                $item_title = $row['title'] ?? null;
                $item_date = $row['givendate'] ?? null;
                $item_tag = $row['tag'] ?? null;

                $date = Carbon::parse($this->transaction_ledger->given_date)->format('Y-m-d');
                $month = Carbon::parse($this->transaction_ledger->month)->startOfMonth()->format('Y-m-d');

                if(isset($item_month) && $item_month !== ''){
                    $month = Carbon::createFromFormat("d/m/Y", $item_month)->startOfMonth()->format('Y-m-d');
                }

                if(isset($item_date) && $item_date !== ''){
                    $date = Carbon::createFromFormat("d/m/Y", $item_date)->format('Y-m-d');
                }

                $description = '';
                if(isset($notes)) $description .= $notes;
                if(isset($this->transaction_ledger->description) && $this->transaction_ledger->description!='') $description .= ($description!=''?' | ':'')  . $this->transaction_ledger->description;

                $title = $this->transaction_ledger->title;

                if(isset($item_title) && $item_title !== ''){
                    $title = trim($item_title);
                }

                $source = null;

                $additional_details = [
                    'date' => $date,
                    'month' => $month,
                    'tag' => $item_tag
                ];

                switch ($sourceType) {
                    case 'addon':
                    case 'booking':
                    case 'driver':
                    case 'vehicle':

                        $namespace = null;
                        $resource_id = null;
                        $driver_id = null;
                        $tag = "transaction_charge";
                        $chargeBooking = true;

                        if(isset($item_tag) && $item_tag !== ''){
                            $tag = strtolower(trim( $item_tag ));
                        }


                        if($sourceType === 'addon'){

                            $chargeBooking = false;



                            $addon = Addon::with([
                                'setting' => function($query){
                                    $query->select('id', 'title', 'types');
                                }
                            ])
                            ->find((int)$sourceId);
                            if(!isset($addon)){
                                $errors[] = "Row #$rowNo - Addon not found against ID ".$sourceId;
                                continue 2;
                            }

                            $addon_type = $row['addon-type'] ?? null;
                            $addon_expense = $row['addon-expense'] ?? null;

                            if(isset($addon_type)){
                                $addon_type = trim($addon_type);
                                $isValid = false;

                                # Validate type
                                if(isset($addon->setting->types) && count($addon->setting->types) > 0){

                                    $isValid = collect($addon->setting->types)
                                    ->contains(function($item) use ($addon_type) {
                                        return strtolower(trim($item['title'])) === strtolower(trim($addon_type));
                                    });
                                }

                                if(!$isValid){
                                    $errors[] = "Row #$rowNo - Addon type not found <br /> Addon Type: <b>" . $addon_type . "</b> <br /> Addon Title: <b>".$addon->setting->title.'</b>';
                                    continue 2;
                                }


                            }


                            if(isset($addon_type) && isset($addon_expense)){

                                # ----------------------
                                #   Add Addon Expense
                                # ----------------------
                                $expense = new AddonExpense;
                                $expense->addon_id = $addon->id;
                                $expense->month = $month;
                                $expense->given_date = $date;
                                $expense->type = $addon_type;
                                $expense->charge_amount = $amount;
                                $expense->description = $description;
                                $expense->amount = $addon_expense;
                                $expense->attachment = null;
                                $expense->save();
                                // When expense Imported Add Status as In Progress and Set Current Stage as Expense Type
                                $addon->status = 'inprogress';
                                $addon->current_stage = $addon_type;
                                $addon->save();

                                #add relations
                                foreach ($this->ledgers as $ledger) {
                                    $relation = new Table_relation;
                                    $relation->ledger_id = $ledger->id;
                                    $relation->source_id = $expense->id;
                                    $relation->source_model = get_class($expense);
                                    $relation->tag = 'addon_expense';
                                    $relation->is_real = false;
                                    $relation->save();
                                }

                                $additional_details['addon_expense'] = $addon_expense;
                                $additional_details['addon_type'] = $addon_type;
                            }

                            # ------------------------------
                            #   (REASON TO REMOVE CODE)
                            # Remove charging addon as well,
                            # we only want to set the
                            # price of addon via AddonExpense
                            # ------------------------------

                            // else{


                            //     # ----------------------
                            //     #   Add Addon Deduction
                            //     # ----------------------

                            //     $charge = new AddonDeduction;
                            //     $charge->addon_id = $addon->id;
                            //     $charge->date = $date;
                            //     $charge->month = $month;
                            //     $charge->description = $description;
                            //     $charge->amount = $amount;

                            //     $charge->save();


                            //     #add relations
                            //     foreach ($this->ledgers as $ledger) {
                            //         $relation = new Table_relation;
                            //         $relation->ledger_id = $ledger->id;
                            //         $relation->source_id = $charge->_id;
                            //         $relation->source_model = get_class($charge);
                            //         $relation->tag = 'addon_charge';
                            //         $relation->is_real = false;
                            //         $relation->save();
                            //     }

                            // }



                            $source = $addon;
                            $title = $addon->setting->title . " Charge";
                            $tag = strtolower( Str::slug( preg_replace('/\s+/', '', $addon->setting->title) ) ) . '_addon';

                        }
                        else if($sourceType === 'booking'){
                            $namespace = "booking";
                            $resource_id = (int)$sourceId;
                            $booking = VehicleBooking::select('id')
                            ->find($resource_id);
                            $source = $booking;
                        }
                        else if($sourceType === 'driver'){
                            $namespace = "booking";

                            $driver_id = (int)$sourceId;

                            $driver = Driver::select('id', 'booking_id')
                            ->find($driver_id);
                            $resource_id = (int)$driver->booking_id;

                            $source = $driver;

                        }
                        else if($sourceType === 'vehicle'){

                            $vehicle = Vehicle::select('id', 'vehicle_booking_id')->where('plate', (string) $sourceId)->limit(1)->first();

                            if(!isset($vehicle)){
                                $vehicle = Vehicle::select('id', 'vehicle_booking_id')
                                ->find((int)$sourceId);
                            }

                            if(!isset($vehicle)){
                                $errors[] = "Row #$rowNo - No Vehicle Found Againt $sourceId";
                                continue 2;

                            }
                            else{
                                if(isset($vehicle->vehicle_booking_id) && $vehicle->vehicle_booking_id !== ''){
                                    // vehicle is assigned to some booking, act as a booking
                                    $namespace = "booking";
                                    $resource_id = (int)$vehicle->vehicle_booking_id;
                                }
                                else{
                                    // Not assigned to any booking, as a vehicle
                                    $namespace = "vehicle";
                                    $resource_id = (int)$vehicle->id;
                                }

                                $source = $vehicle;
                            }
                        }


                        # ----------------------------------
                        # if "charged-on" column is found,
                        #  : means charge is scheduled
                        # ----------------------------------
                        if($chargeBooking && ( isset($row['charged-on']) || isset($row['chargedon']) ) ){
                            $chargedon = $row['charged-on'] ?? $row['chargedon'];
                            if(isset($chargedon) && trim($chargedon) !== ''){
                                $chargedon = Carbon::createFromFormat( 'd/m/Y', $chargedon)->format('Y-m-d');

                                $additional_details['chargedon'] = $chargedon;

                                # Check if date is passed, we need to charge it
                                if(Carbon::now()->greaterThan($chargedon)){
                                    $additional_details['charged'] = true;
                                }
                                else{

                                    # Save data to be charged later
                                    $additional_details['charged'] = false;
                                    $additional_details['chargeddata'] = [
                                        'namespace' => $namespace,
                                        'resource_id' => $resource_id,
                                        'title' => $title,
                                        'description' => $description,
                                        'type' => 'dr',
                                        'tag' => $tag,
                                        'date' => $date,
                                        'month' => $month,
                                        'driver_id' => $driver_id,
                                        'chargeBooking' => $chargeBooking,

                                    ];

                                    # Skip charging, it will be charged via a cron
                                    $chargeBooking = false;


                                }

                            }
                        }

                        # --------------------------
                        #   Add Vehicle Ledger Item
                        # --------------------------

                        if($chargeBooking){

                            $vLedger = VehicleLedger::ofNamespace($namespace, $resource_id)->get()->first();


                            if(!isset($vLedger)){
                                $vLedger = new VehicleLedger;
                                $vLedger->vehicle_booking_id = $namespace === "booking" ? (int)$resource_id : null;
                                $vLedger->vehicle_id = $namespace === "vehicle" ? (int)$resource_id : null;
                                $vLedger->save();
                            }

                            $vItemObj = (object)[
                                'title' => $title,
                                'description' => $description,
                                'type' => "dr",
                                'tag' => $tag,
                                'date' => $date,
                                'month' => $month,
                                'amount' => $amount
                            ];

                            $vItemObj->attachment = null;

                            if(isset($driver_id)){
                                $vItemObj->driver_id = $driver_id;
                            }

                            $vLedgerItem =  $vLedger->addItem($vItemObj);

                            #add relations
                            foreach ($this->ledgers as $ledger) {
                                $relation = new Table_relation;
                                $relation->ledger_id = $ledger->id;
                                $relation->source_id = $vLedgerItem->_id;
                                $relation->source_model = get_class($vLedgerItem);
                                $relation->tag = 'statementledger_transaction';
                                $relation->is_real = false;
                                $relation->save();
                            }

                        }

                        break;

                    default:
                        break;
                }

                if(!isset($source)) continue;


                # -----------------------
                #   Insert Charge Record
                # -----------------------
                $transaction_detail = new TransactionLedgerDetails;
                $transaction_detail->tl_id = $this->transaction_ledger->id;
                $transaction_detail->source_id = $source->id;
                $transaction_detail->source_model = get_class($source);
                $transaction_detail->description = $item_title ?? $notes;
                $transaction_detail->amount = $amount;
                $transaction_detail->additional_details = $additional_details;
                $transaction_detail->save();


                #add relations
                foreach ($this->ledgers as $ledger) {
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $transaction_detail->_id;
                    $relation->source_model = get_class($transaction_detail);
                    $relation->tag = 'transaction_detail';
                    $relation->is_real = false;
                    $relation->save();
                }


            }


            # ---------------------------------
            #   Show errors
            #   This will act as warning
            #   since only error rows skipped
            # ---------------------------------
            if(count($errors) > 0){
                throw ValidationException::withMessages($errors);
            }
        }

    }

    /**
    *   Handle Payable records
    *
    * @param \Illuminate\Support\Collection $rows
    *
    * @return null
    */
    private function handlePayable(Collection $rows) : void
    {

        # Perform basic header validation on 1st row
        $errorFound = $this->headersValidation([
            'date',
            'amount',
            'status',
            'account'
        ], $rows->first()->toArray());

        if(!$errorFound){
            $rows = $rows->map(function($item){
                $item['amount'] = (float)str_replace(",", "", $item['amount']);
                return $item;
            });
            $accountHandles = $rows
            ->pluck('account')
            ->unique()
            ->filter(function ($value, $key) {
                return isset($value) && $value !== '';
            })
            ->values();

            $accounts = Account::whereIn('handle', $accountHandles)->get();

            # ------------------
            # Validate accounts
            # ------------------
            foreach ($rows->groupBy('account') as $account_handle => $account_rows) {

                if(isset($account_handle) && $account_handle !== ''){


                    $account = $accounts->where('handle', $account_handle)->first();
                    if(!isset($account)){
                        throw ValidationException::withMessages(["1" => "Account not found against ".$account_handle]);
                        return;
                    }


                    $total_amount = $account_rows
                    ->sum('amount');

                    if(!app('helper_service')->routes->has_custom_access('negative_account_balance', [$account->id]) && $total_amount < 0 && $account->balance < abs($total_amount) ){
                        throw ValidationException::withMessages(["1" => "Insufficient balance in <b>".$account->title.":</b> Deduction is ".abs($total_amount)." and remaining balance is ".$account->balance]);
                        return;
                    }
                }

            }

            # Update calculated amount
            $total_amount = $rows->sum('amount');
            $this->transaction_ledger->amount = $total_amount;
            $this->transaction_ledger->update();
            # ----------------------------------
            #   Insert Ledgers Against each row
            # ----------------------------------
            foreach ($rows as $row) {

                $amount = $row['amount'];
                $account_handle = $row['account'];
                $status = $row['status'];
                $date = Carbon::createFromFormat( 'd/m/Y', trim($row['date']))->format('Y-m-d');
                $month = Carbon::parse($date)->startOfMonth()->format('Y-m-d');


                $account = null;

                if(isset($account_handle) && $account_handle !== ''){
                    $account = $accounts->where('handle', $account_handle)->first();
                }

                # Save ledger
                $ledger = new Ledger;
                $ledger->type="dr";
                $ledger->source_id=$this->transaction_ledger->id;
                $ledger->source_model=get_class($this->transaction_ledger);
                $ledger->date=$date;
                $ledger->tag="transaction_ledger";
                $ledger->month = $month; // For Filteration Purpose
                $ledger->is_cash=strtolower($status) === "pending" ? false : true;
                $ledger->amount=$amount;
                $ledger->props=[
                    'by'=>Auth::user()->id,
                    'import' => true,
                    'account'=>[
                        'id'=>$account->_id,
                        'title'=>$account->title
                    ]
                ];
                $ledger->save();

                $this->ledgers[] = $ledger;

                $this->parent->_IH_addRecord(get_class($ledger), $ledger->id);


                #add relations
                $relation = new Table_relation;
                $relation->ledger_id = $ledger->id;
                $relation->source_id = $this->transaction_ledger->id;
                $relation->source_model = get_class($this->transaction_ledger);
                $relation->tag = 'transaction_ledger';
                $relation->is_real = true;
                $relation->save();

            }

            #add relations
            foreach ($this->ledgers as $ledger) {
                $otherLedgers = collect($this->ledgers)->where('id', '!=', $ledger->id);
                foreach ($otherLedgers as $otherLedger) {
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $otherLedger->id;
                    $relation->source_model = get_class($otherLedger);
                    $relation->tag = 'ledger';
                    $relation->is_real = false;
                    $relation->save();

                }
            }

            # ------------------
            #   Insert Records
            # ------------------
            foreach ($rows as $index => $row) {
                $amount = $row['amount'];
                $account_handle = $row['account'];
                $is_cheque = isset($row['cheque']) && $row['cheque'] == 1 ? true : false;
                $status = $row['status'];

                $tr_date = Carbon::createFromFormat( 'd/m/Y', trim($row['date']))->format('Y-m-d');

                $date = Carbon::parse($this->transaction_ledger->given_date)->format('Y-m-d');
                $month = Carbon::parse($this->transaction_ledger->month)->startOfMonth()->format('Y-m-d');


                $account = null;

                if(isset($account_handle) && $account_handle !== ''){
                    $account = $accounts->where('handle', $account_handle)->first();
                }

                # Genrating Description
                $tr_description = Carbon::parse($date)->format('d M, Y');
                if($is_cheque) $tr_description .= ' | Cheque';
                if(isset($this->transaction_ledger->description) && $this->transaction_ledger->description!='') $tr_description .= ' | '. $this->transaction_ledger->description;

                $links = [];

                if(isset($this->ledgers[$index])){
                    $links[] = [
                        'modal'=>get_class(new Ledger),
                        'id'=>$this->ledgers[$index]->id,
                        'tag'=>'ledger'
                    ];
                }

                # Create account transaction
                $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
                    'account_id' => $account->id,
                    'type'=>"dr",
                    'date' => $tr_date,
                    'title'=>$this->transaction_ledger->title,
                    'description'=>$tr_description,
                    'tag'=>'transaction_ledger',
                    'status' => strtolower($status) === "pending" ? "pending" : "paid",
                    'amount'=>$amount,
                    'additional_details' => [
                        "tl_id" => $this->transaction_ledger->id,
                        "is_cheque" => $is_cheque,
                        'charge_date' => $tr_date
                    ],
                    'links'=>$links
                ]);

                #add relations
                foreach ($this->ledgers as $ledger) {
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $transaction->id;
                    $relation->source_model = get_class($transaction);
                    $relation->tag = 'transaction';
                    $relation->is_real = false;
                    $relation->save();
                }

            }

        }
    }

    public function headersValidation($valid, $imported): bool
    {
        $errors = null;
        $index = 1;
        foreach ($valid as $key => $valid_value) {
            if(!array_key_exists($valid_value, $imported)) {
                $errors["#".($index++)] = "Missing or wrong header: \"$valid_value\"";
            }
        }

        if(isset($errors)) {
            throw ValidationException::withMessages($errors);

            return true;
        }

        return false;
    }


    public function chunkSize(): int
    {
        return 1000;
    }

}
