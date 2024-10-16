<?php

namespace App\Imports;

use App\Accounts\Models\Account;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Client;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Installment;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\VehicleLedgerItem;
use App\Traits\ImportHistoryTrait;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class IncomesImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{

    use RemembersChunkOffset, ImportHistoryTrait;

    private Request $request;

    public function __construct(Request $request)
    {

        $this->request = $request;

        HeadingRowFormatter::extend('tmp', function($value, $key) {

            return $key;
        });
        HeadingRowFormatter::default('tmp');
    }

    /**
    * @param Illuminate\Support\Collection $rows
    *
    * @return null
    */
    public function collection(Collection $rows): void
    {

        if($rows->count() === 0) return;

        # ---------------
        # Payload
        # ---------------

        $request = $this->request;

        $mapper = collect($request->heading_mapper);
        $account = Account::findOrFail($request->account_id);

        $month = Carbon::parse($request->month)->startOfMonth();

        $end = null;
        if(isset($request->end_date) && $request->end_date !== ''){
            $end = Carbon::parse($request->end_date);
        }
        $start = null;
        if(isset($request->start_date) && $request->start_date !== ''){
            $start = Carbon::parse($request->start_date);
        }

        if(!isset($end) && !isset($start)){
            // Monthly payment
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
        }

        if(!isset($end)){
            $end = Carbon::parse($request->start_date);
        }

        $received_amount = (float) $request->get('received_amount', 0);

        $client = Client::with([
            'entities' => function($query) use ($start, $end){

                # Match the range and fetch riders accordingly

                # Bind 2 where into 1
                $query->where(function ($q) use ($start, $end) {
                    # This where will filter histories with "active" entities
                    $q->where(function ($q2) use ($end) {
                        $q2->where('assign_date', '<=', $end)
                        ->whereNull('unassign_date');
                    })
                    # This where will filter histories with "inactive" entities
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('assign_date', '<=', $end)
                        ->where('unassign_date', '>=', $start)
                        ->whereNotNull('unassign_date');
                    });
                });
            },
            'entities.source'
        ])
        ->findOrFail((int) $request->client_id);

        // ddd($client->toArray());

        $isWeekly = false;
        $isMonthly = false;
        $isDaily = false;
        if($start->diffInDays($end) >= 6 && $start->diffInDays($end) <= 8){
            $isWeekly = true;
        }
        if($start->diffInDays($end) === 0){
            $isDaily = true;
        }
        if($start->equalTo($month->copy()->startOfMonth()) && $end->equalTo($month->copy()->endOfMonth())){
            $isMonthly = true;
        }

        $errors = [];

        $rows = $rows->map(function($row, $rowIndex) use ($mapper, $client, &$errors){

            $rowNumber = $rowIndex + 2;

            $refid = null;
            $driver_earning = null;
            $company_earning = null;
            $amount = null;
            $descriptions = [];
            $raw = [];

            foreach ($row as $index => $value) {
                $mapItem = $mapper->where('source', $index)->first();
                if(isset($mapItem)){
                    $destination = $mapItem['destination'];
                    $title = $mapItem['title'];

                    if($destination === "refid" && !isset($refid)){
                        $refid = $value;
                    }
                    if($destination === "driver_earning" && !isset($driver_earning)){
                        $driver_earning = app('helper_service')->helper->parseNumber($value);
                    }
                    if($destination === "company_earning" && !isset($company_earning)){
                        $company_earning = app('helper_service')->helper->parseNumber($value);
                    }
                    if($destination === "amount" && !isset($amount)){
                        $amount = app('helper_service')->helper->parseNumber($value);
                    }
                    if($destination === "description"){
                        if(isset($value) && trim($value)!='') $descriptions[] = "$title: $value";
                        if(isset($value) && trim($value)!='') $raw[] = [
                            'key' => strtolower( Str::slug( preg_replace('/\s+/', '', $title))),
                            'value' => $value
                        ];
                    }

                }
            }

            $entity = $client->entities->where('refid', $refid)->first();

            if(!isset($entity)){
                $errors[] = "row#$rowNumber - $refid | Driver Not found against client $client->name";
            }

            return (object)[
                'refid' => $refid,
                'entity' => isset($entity) ? $entity : null,
                'company_earning' => isset($company_earning) ? (float)$company_earning : null,
                'driver_earning' => isset($driver_earning) ? (float)$driver_earning : null,
                'amount' => isset($amount) ? (float)$amount : 0,
                'description' => implode("<br />", $descriptions),
                'raw' => $raw
            ];



        });

        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        $this->_IH_init("income");
        $this->_IH_addData([
            'total_records' => $rows->count()
        ]);


        $total_company_earning = round($rows->whereNotNull('company_earning')->sum('company_earning'), 2);
        $total_driver_earning = round($rows->whereNotNull('driver_earning')->sum('driver_earning'), 2);
        $total_amount = round($rows->sum('amount'), 2);

        $baseTitle = $client->name.' Payout';
        if($isWeekly){
            $baseTitle = $client->name.' Payout Week ( '.$start->format('d M Y').' - '.$end->format('d M Y').' )';
        }
        else if($isDaily){
            $baseTitle = $client->name.' Payout ( '.$start->format('d M Y').' )';
        }
        else if($isMonthly){
            $baseTitle = $client->name.' Payout ( '.$month->format('M Y').' )';
        }

        $description = trim("".($rows->whereNotNull('driver_earning')->count() > 0 ? "Driver Earning: $total_driver_earning" : "")."
        ".($rows->whereNotNull('company_earning')->count() > 0 ? "Company Earning: $total_company_earning" : "")."
        Bank Received: $total_amount
        ");

        $transactionLedger = new TransactionLedger;
        $transactionLedger->title = $baseTitle;
        $transactionLedger->given_date = Carbon::now()->format('Y-m-d');
        $transactionLedger->month = $month->format('Y-m-d');
        $transactionLedger->amount = $total_amount;
        $transactionLedger->description = $description;
        $transactionLedger->by = Auth::user()->id;
        $transactionLedger->tag = "income";
        $transactionLedger->save();


        # Save ledger
        $ledger = new Ledger;
        $ledger->type="cr";
        $ledger->source_id=$transactionLedger->id;
        $ledger->source_model=get_class($transactionLedger);
        $ledger->date=$transactionLedger->given_date;
        $ledger->tag="client_income";
        $ledger->month = $transactionLedger->month; // For Filteration Purpose
        $ledger->is_cash= $received_amount > 0 ? true : false;
        $ledger->amount = $received_amount > 0 ? $received_amount : $total_amount;
        $ledger->props=[
            'by'=>Auth::user()->id,
            'import' => true,
            'account'=>[
                'id'=>$account->_id,
                'title'=>$account->title
            ]
        ];
        $ledger->save();

        $this->_IH_addRecord(get_class($ledger), $ledger->id);

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transactionLedger->id;
        $relation->source_model = get_class($transactionLedger);
        $relation->tag = 'client_income';
        $relation->is_real = true;
        $relation->save();

        $transaction_payload = [
            'account_id' => $account->id,
            'type'=>"cr",
            'date' => $transactionLedger->given_date,
            'title'=>$transactionLedger->title,
            'description'=>$transactionLedger->description,
            'tag'=>'client_income',
            'status' => "pending",
            'real_amount'=>$total_amount,
            'additional_details' => [
                "tl_id" => $transactionLedger->id,
                "client_id" => $client->id,
                "is_cheque" => 0,
                'charge_date' => $transactionLedger->given_date
            ],
            'links'=>[
                [
                    'modal'=>get_class(new TransactionLedger),
                    'id'=>$transactionLedger->id,
                    'tag'=>'client_income'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ]
            ]
        ];

        if($received_amount > 0){
            $transaction_payload['status'] = "paid";
            $transaction_payload['amount'] = $received_amount;
        }

        # Create account transaction
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction($transaction_payload);

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = false;
        $relation->save();


        # --------------------------------
        #   Save Incomes - Add to bookings
        # --------------------------------
        foreach ($rows as $row) {

            $amount = round($row->amount, 2);
            $driver_earning = round($row->driver_earning, 2);
            $company_earning = round($row->company_earning, 2);
            $entity= $row->entity;
            $refid= $row->refid;
            $description= $row->description;
            $raw= $row->raw;

            $group = 'client'.$client->id;

            $resource_id = null;

            $source = $entity->source;
            if($entity->source_model == Driver::class){
                $resource_id = $source->id;
                $group .= '_driver'.$resource_id;
            }

            if(!isset($resource_id)){
                throw ValidationException::withMessages(["1" => "Source not assigned to driver - ID ".$refid]);
                return;
            }

            $resource_id = (int) $resource_id;

            
            $refId_title = 'RefID';
            if(str_contains(strtolower($client->name), "careem")) $refId_title = 'Captain ID';
            if(str_contains(strtolower($client->name), "uber")) $refId_title = 'Uber ID';
            if(str_contains(strtolower($client->name), "talabat")) $refId_title = 'FEID';
            if(str_contains(strtolower($client->name), "deliveroo")) $refId_title = 'FEID';
            // if(str_contains(strtolower($client->name), "atm")) $refId_title = 'Card Number';

            $full_description = trim("
            $refId_title: $refid
            $description
            ");

            # --------------------------
            #   Add Statement Ledger Item
            # : TO => DRIVER ACCOUNT
            # --------------------------

            $vLedger = new StatementLedger;
            $exists = StatementLedger::ofNamespace('driver', $resource_id)->first();;
            if(isset($exists)) $vLedger = $exists;
            else{
                $vLedger->linked_to = 'driver';
                $vLedger->linked_id = $resource_id;
                $vLedger->save();
            }


            $vItemObj = (object)[
                'title' => $baseTitle,
                'description' => $full_description,
                'type' => "cr",
                'group' => $group,
                'tag' => strtolower( Str::slug( preg_replace('/\s+/', '', $client->name) ) ) . "_income",
                'channel' => "import",
                'date' => $end->format('Y-m-d'),
                'month' => $month->format('Y-m-d'),
                'amount' => $driver_earning,
                'additional_details' => [
                    'is_weekly' => $isWeekly,
                    'is_daily' => $isDaily,
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                    'refid' => $refid,
                    'driver_earning' => $driver_earning,
                    'company_earning' => $company_earning,
                    'total_amount' => $amount,
                    'raw' => $raw
                ]
            ];

            $vItemObj->attachment = null;

            $vLedgerItem =  $vLedger->addItem($vItemObj);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'income_statementledger_driver';
            $relation->is_real = false;
            $relation->save();


            # --------------------------
            #   Add Statement Ledger Item
            # : TO => Company Account
            # --------------------------

            $vLedger = new StatementLedger;
            $exists = StatementLedger::ofNamespace('company', $resource_id)->first();;
            if(isset($exists)) $vLedger = $exists;
            else{
                $vLedger->linked_to = 'company';
                $vLedger->linked_id = $resource_id;
                $vLedger->save();
            }

            $vItemObj->type = 'cr'; // Credit the amount
            $vItemObj->amount = $company_earning; // Change the amount
            
            $vLedgerItem =  $vLedger->addItem($vItemObj);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->_id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'income_statementledger_company';
            $relation->is_real = false;
            $relation->save();


            # -----------------------
            #   Insert Record
            # -----------------------
            $transaction_detail = new TransactionLedgerDetails;
            $transaction_detail->tl_id = $transactionLedger->id;
            $transaction_detail->source_id = $source->id;
            $transaction_detail->source_model = get_class($source);
            $transaction_detail->description = $description;
            $transaction_detail->amount = $amount;
            $transaction_detail->additional_details = $vItemObj->additional_details;
            $transaction_detail->save();


            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $transaction_detail->_id;
            $relation->source_model = get_class($transaction_detail);
            $relation->tag = 'transaction_detail';
            $relation->is_real = false;
            $relation->save();

        }

        $this->_IH_end();


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
