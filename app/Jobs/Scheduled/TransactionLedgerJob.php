<?php

# ---------------------------------------------------
# CMD: php artisan tinker -> \App\Jobs\TestingJob::dispatchSync();
# LOGS PATH: /var/www/limo.buraqalliance.com/storage/logs/crons/test-{YYYY-MM-DD}.log
# ---------------------------------------------------



namespace App\Jobs\Scheduled;

use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\VehicleLedger;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TransactionLedgerJob implements ShouldQueue
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

        Log::setDefaultDriver('transactionledger-cron'); // Set Default Driver

        Log::info("------------------------ [Transaction Legder ( START )] ------------------------");

        # ----------------
        #    Variables
        # ----------------
        $current = 1;
        $chunk = 50;

        # -----------------------
        #    Process in chunks
        # -----------------------
        TransactionLedgerDetails::where('additional_details.charged', false)
        ->where('additional_details.charged', 'exists', true)
        // ->where('additional_details.chargedon', '<=', new DateTime())
        ->with('table_relations')
        ->orderBy('_id')
        ->chunk($chunk, function ($transaction_details) use (&$current, $chunk) {

            $processed = 0;
            $ids = [];
            foreach ($transaction_details as $transaction_detail) {

                if( isset($transaction_detail['additional_details']) && isset($transaction_detail['additional_details']['chargeddata']) ){
                    $charged_on = $transaction_detail['additional_details']['chargedon'];

                    if(isset($charged_on) && trim($charged_on) !== '' && Carbon::parse($charged_on)->lessThanOrEqualTo(Carbon::now())){

                        $charged_data = $transaction_detail['additional_details']['chargeddata'];

                        $namespace = $charged_data['namespace'];
                        $resource_id = $charged_data['resource_id'];
                        $title = $charged_data['title'];
                        $description = $charged_data['description'];
                        $tag = $charged_data['tag'];
                        $date = $charged_data['date'];
                        $month = $charged_data['month'];
                        $driver_id = $charged_data['driver_id']??null;
                        $amount = $transaction_detail->amount;

                        # --------------------------------
                        # Charge Amount
                        #   : Add Vehicle Ledger Item
                        # --------------------------------
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

                        # VLI = vehicle_ledger_items | TD = transaction_item_details
                        # ----
                        # Since count of VLI & TD are same
                        # We can use the ledgers of TD and link it to VLI

                        foreach ($transaction_detail->table_relations as $table_relation) {
                            $relation = new Table_relation;
                            $relation->ledger_id = $table_relation->ledger_id;
                            $relation->source_id = $vLedgerItem->_id;
                            $relation->source_model = get_class($vLedgerItem);
                            $relation->tag = 'statementledger_transaction';
                            $relation->is_real = false;
                            $relation->save();
                        }

                        $ids[] = $transaction_detail->id;

                        $processed ++;
                    }
                }

            }

            # ---------------------
            # Update Record
            #   : Mark as charged
            # ---------------------

            $nUpdated = false;
            if(count($ids) > 0){
                $nUpdated = TransactionLedgerDetails::whereIn('_id', $ids)->update([
                    'additional_details.charged' => true
                ]);
            }

            $total = count($transaction_details);
            $now = ($current * $chunk) - $chunk;
            $next = $current * $chunk;


            Log::info( "CHUNK:[$now of $next] _ PROCESSED:[$processed of $total] _ Updated: ".($nUpdated ? "Y" : "N") );

            $current ++;
        });

        if($current === 1){
            Log::info("No Records found!");
        }

        Log::info("-------------------------- [Transaction Legder ( END )] --------------------------");
    }

}
