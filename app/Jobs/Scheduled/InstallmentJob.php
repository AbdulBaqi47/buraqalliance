<?php

# ---------------------------------------------------
# CMD: php artisan tinker -> \App\Jobs\TestingJob::dispatchSync();
# LOGS PATH: /var/www/limo.kinglimousine.ae/storage/logs/crons/test-{YYYY-MM-DD}.log
# ---------------------------------------------------



namespace App\Jobs\Scheduled;

use App\Accounts\Models\Account;
use App\Helpers\Suppliers;
use App\Http\Middleware\HttpMacros;
use App\Models\Tenant\Addon;
use App\Models\Tenant\AddonDeduction;
use App\Models\Tenant\AddonExpense;
use App\Models\Tenant\Brand;
use App\Models\Tenant\Collection;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Installment;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\PriceModifier;
use App\Models\Tenant\Product;
use App\Models\Tenant\Shop;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\Variant;
use App\Models\Tenant\VariantInventory;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\Warehouse;
use App\Models\Tenant\WorkerProgress;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class InstallmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 86400;


    /**
     * Create a new job instance.
     *
     * @param  array $products
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);

        Log::setDefaultDriver('installment-cron'); // Set Default Driver

        Log::info("------------------------ [Installments ( START )] ------------------------");

        $installmentsGroup = Installment::with([
            'source'
        ])
        ->whereNull('transaction_ledger_id')
        ->where('charge_date', '<=', new DateTime())
        ->get();

        $accountIds = $installmentsGroup->pluck('account_id')->unique()->values()->toArray();
        $accounts = Account::whereIn('_id', $accountIds)->get();



        # Add single transaction against this group
        $installmentsGroup = $installmentsGroup->groupBy('code');

        $total_installments_groups = count($installmentsGroup);

        Log::info("Master Installments Found: ".$total_installments_groups);

        foreach ($installmentsGroup as $code => $installments) {

            Log::info("__________________ [Processing - Code: $code ( START )] __________________");

            $total_installments_by_code = count($installments);

            Log::info("Code: ".$code);
            Log::info("Count: ".$total_installments_by_code);

            $singleInstallmentsGroup = $installments->groupBy('pay_date');

            foreach ($singleInstallmentsGroup as $date => $singleInstallments) {

                $pay_date = Carbon::parse($date)->format('Y-m-d');

                Log::info("######## [Processing - DATE: $pay_date ( START )] ########");

                $total_installments_by_date = count($singleInstallments);

                Log::info("Date: ".$pay_date);
                Log::info("Count: ".$total_installments_by_date);

                # -----------------------------------
                #   Velidate Records - Charge Amounts
                # -----------------------------------
                foreach ($singleInstallments as $singleInstallment) {
                    $amount = $singleInstallment->charge_amount;
                    $sourceType = $singleInstallment->source_model;
                    $sourceId = $singleInstallment->source_id;

                    $source = $sourceType::find($sourceId);

                    if(!isset($source)){

                        Log::error("$sourceType:$sourceId Not found");

                    }
                    else{
                        $singleInstallment->source = $source;

                        $number = null;

                        # Get installment number
                        # Fetch data against this source, get the index of current installment being charged
                        Installment::where('code', $code)
                        ->where('source_id', $sourceId)
                        ->where('source_model', $sourceType)
                        ->get()
                        ->each(function ($item, int $key) use ($singleInstallment, &$number) {
                            if($item->id === $singleInstallment->id){
                                $number = $key;
                                return false; // Stop the loop
                            }
                        });

                        $singleInstallment->number = ($number + 1);

                        Log::info("[".$sourceType. ' / '.$sourceId."] Installment Number: ".$singleInstallment->number);
                    }
                }



                # Add installments to:
                #   Ledger - So amount can be "pay"
                #   TransactionLedger - So amount can be "pay"
                #   AccountTransaction - So amount can be "pay"
                #   ------
                #   TransactionLedgerDetails - So amount can be "charged"
                #   AddonDeduction - So amount can be "charged"

                $first_installment = $singleInstallments->first();
                $account_id = $first_installment->account_id;
                $by = $first_installment->by ?? null;
                $account = $accounts->where('_id', $account_id)->first();

                $total_pay_amount = $singleInstallments->sum('pay_amount');
                $total_charge_amount = $singleInstallments->sum('charge_amount');

                # -----------------------------
                #   TEMP CODE
                # -----------------------------
                if(Carbon::parse($first_installment->created_at)->lessThanOrEqualTo("2023-09-01")){
                    # Records having pay date less then this date, will be charge according to charge date
                    $pay_date = Carbon::parse($first_installment->charge_date)->format('Y-m-d');

                }

                # -----------------------------
                #   Insert in Master Table
                # -----------------------------
                $transactionLedger = new TransactionLedger;
                $transactionLedger->title = "Installments | Count: ".$total_installments_by_date;
                $transactionLedger->given_date = Carbon::parse($pay_date)->format('Y-m-d');
                $transactionLedger->month = Carbon::parse($pay_date)->startOfMonth()->format('Y-m-d');
                $transactionLedger->amount = $total_pay_amount;
                $transactionLedger->description = "Paid: $total_pay_amount <br /> Charged: $total_charge_amount";
                $transactionLedger->by = $by;
                $transactionLedger->tag = "installment";
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
                $ledger->amount=$total_pay_amount;
                $ledger->props=[
                    'by'=>$by,
                    'import' => true,
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
                    'date' => $pay_date,
                    'title'=>$transactionLedger->title,
                    'description'=>$transactionLedger->description,
                    'tag'=>'transaction_ledger',
                    'status' => "pending",
                    'transaction_by' => $by,
                    'amount'=>$total_pay_amount,
                    'additional_details' => [
                        "tl_id" => $transactionLedger->id,
                        "is_cheque" => true,
                        'charge_date' => $pay_date
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



                # -----------------------------------
                #   Insert Records - Charge Amounts
                # -----------------------------------
                foreach ($singleInstallments as $rowIndex => $singleInstallment) {
                    $rowNo = $singleInstallment->id;
                    $amount = $singleInstallment->charge_amount;
                    $sourceType = $singleInstallment->source_model;
                    $sourceId = $singleInstallment->source_id;

                    # OLD CODE: We were adding dates according to "Pay Date"
                    // $date = Carbon::parse($transactionLedger->given_date)->format('Y-m-d');
                    // $month = Carbon::parse($transactionLedger->month)->startOfMonth()->format('Y-m-d');

                    # NEW CODE: Now we will add date according to "Charge Date"
                    $date = Carbon::parse($singleInstallment->charge_date)->format('Y-m-d');
                    $month = Carbon::parse($singleInstallment->charge_date)->startOfMonth()->format('Y-m-d');

                    $description = '';
                    // if(isset($notes)) $description .= $notes;
                    // if(isset($transactionLedger->description) && $transactionLedger->description!='') $description .= ($description!=''?' | ':'')  . $transactionLedger->description;

                    $title = "Bank Installment ( ".($singleInstallment->number??"")." / ".Carbon::parse($month)->format('M Y')." )";

                    if(!isset($singleInstallment->source)) continue;

                    $source = $singleInstallment->source;


                    switch ($sourceType) {
                        case Addon::class:
                        case VehicleBooking::class:
                        case Driver::class:
                        case Vehicle::class:

                            $namespace = null;
                            $resource_id = null;
                            $driver_id = null;
                            $tag = "installment";

                            $chargeBooking = true;

                            if($sourceType === Addon::class){

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
                                        Log::error("Row #$rowNo - Addon type not found <br /> Addon Type: <b>" . $addon_type . "</b> <br /> Addon Title: <b>".$addon->setting->title.'</b>');
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

                                    #add relations
                                    $relation = new Table_relation;
                                    $relation->ledger_id = $ledger->id;
                                    $relation->source_id = $expense->id;
                                    $relation->source_model = get_class($expense);
                                    $relation->tag = 'addon_expense';
                                    $relation->is_real = false;
                                    $relation->save();

                                    $additional_details = [
                                        'addon_expense' => $addon_expense,
                                        'addon_type' => $addon_type,
                                    ];
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
                            else if($sourceType === VehicleBooking::class){
                                $namespace = "booking";
                                $resource_id = (int)$sourceId;
                                $booking = VehicleBooking::select('id')
                                ->find($resource_id);
                                $source = $booking;
                            }
                            else if($sourceType === Driver::class){
                                $namespace = "booking";

                                $driver_id = (int)$sourceId;

                                $driver = Driver::select('id', 'booking_id')
                                ->find($driver_id);
                                $resource_id = (int)$driver->booking_id;

                                $source = $driver;

                            }
                            else if($sourceType === Vehicle::class){
                                if(isset($source->vehicle_booking_id) && $source->vehicle_booking_id !== ''){
                                    // vehicle is assigned to some booking, act as a booking
                                    $namespace = "booking";
                                    $resource_id = (int)$source->vehicle_booking_id;
                                }
                                else{
                                    // Not assigned to any booking, as a vehicle
                                    $namespace = "vehicle";
                                    $resource_id = (int)$source->id;
                                }
                            }

                            if($chargeBooking){


                                # --------------------------
                                #   Add Vehicle Ledger Item
                                # --------------------------

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
                                    'amount' => $amount,
                                    'additional_details' => [
                                        "installment_number" => $singleInstallment->number,
                                        "charge_date" => Carbon::parse($singleInstallment->charge_date)->format('Y-m-d'),
                                        "pay_date" => Carbon::parse($singleInstallment->pay_date)->format('Y-m-d'),
                                    ],
                                ];

                                $vItemObj->attachment = null;

                                if(isset($driver_id)){
                                    $vItemObj->driver_id = $driver_id;
                                }

                                $vLedgerItem =  $vLedger->addItem($vItemObj);

                                #add relations
                                $relation = new Table_relation;
                                $relation->ledger_id = $ledger->id;
                                $relation->source_id = $vLedgerItem->_id;
                                $relation->source_model = get_class($vLedgerItem);
                                $relation->tag = 'statementledger_transaction';
                                $relation->is_real = false;
                                $relation->save();

                            }

                            break;

                        default:
                            break;
                    }


                    # -----------------------
                    #   Insert Charge Record
                    # -----------------------
                    $transaction_detail = new TransactionLedgerDetails;
                    $transaction_detail->tl_id = $transactionLedger->id;
                    $transaction_detail->source_id = $source->id;
                    $transaction_detail->source_model = get_class($source);
                    $transaction_detail->description = $description;
                    $transaction_detail->amount = $amount;
                    $transaction_detail->save();


                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $transaction_detail->_id;
                    $relation->source_model = get_class($transaction_detail);
                    $relation->tag = 'transaction_detail';
                    $relation->is_real = false;
                    $relation->save();


                    # Save Transaction Ledger ID
                    $singleInstallment->transaction_ledger_id = $transactionLedger->id;
                    $singleInstallment->update();

                }


                Log::info("######### [Processing - DATE: $pay_date ( END )] #########");
            }





            Log::info("___________________ [Processing - Code: $code ( END )] ___________________");

        }

        Log::info("-------------------------- [Installments ( END )] --------------------------");
    }

}
