<?php

namespace App\Imports;

use App\Accounts\Models\Account;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Client;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Employee_ledger;
use App\Models\Tenant\Installment;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Sim;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
use App\Models\Tenant\TransactionLedgerDetails;
use App\Models\Tenant\User;
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

class SimbillsImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows
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
        $paid_amount = (float) $request->get('paid_amount', 0);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $sims = Sim::with([
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
        ->get();


        $errors = [];
        $total_amount = 0;
        $total_count = 0;
        $rows = $rows->flatMap(function($row, $rowIndex) use ($mapper, $sims, $start, $end, &$errors, &$total_amount, &$total_count){

            $rowNumber = $rowIndex + 2;

            $sim_number = null;
            $base = null;
            $amount = null;
            $descriptions = [];

            foreach ($row as $index => $value) {
                $mapItem = $mapper->where('source', $index)->first();
                if(isset($mapItem)){
                    $destination = $mapItem['destination'];
                    $title = $mapItem['title'];

                    if($destination === "sim_number" && !isset($sim_number)){
                        $sim_number = $value;
                    }
                    if($destination === "base" && !isset($base)){
                        $base = $value;
                    }
                    if($destination === "amount" && !isset($amount)){
                        $amount = $value;
                    }
                    if($destination === "description"){
                        if(isset($value) && trim($value)!='') $descriptions[] = "$title: $value";
                    }

                }
            }

            $sim = $sims->where('number', trim($sim_number))->first();

            if(!isset($sim)){
                $errors[] = "row#$rowNumber - $sim_number | Sim not found";

                return [];
            }
            else{
                # ----------------------------------------
                # Split bill to each entity based on usage
                # ----------------------------------------

                $basePayload = (object)[
                    'sim_number' => $sim_number,
                    'sim' => isset($sim) ? $sim : null,
                    'base' => isset($base) ? (float)$base : null,
                    'amount' => isset($amount) ? (float)$amount : 0,
                    'description' => implode("<br />", $descriptions),
                ];

                $total_amount += $basePayload->amount;
                $total_count ++;

                $entities = $sim->entities
                ->map(function($entity) use ($start, $end, $basePayload){

                    # Helping variables
                    $days_in_month = $start->daysInMonth;
                    $allowed_balance = (float)$entity->allowed_balance;

                    # Get no of days rider had this sim
                    $assign_date = Carbon::parse($entity->assign_date);
                    $unassign_date = $entity->status == 'inactive' ? Carbon::parse($entity->unassign_date) : null;
                    if ($assign_date->lessThan($start)) { #assign date will be start of month
                        $assign_date = $start->copy();
                    }
                    if ( (isset($unassign_date) && Carbon::parse($entity->unassign_date)->greaterThan($end)) || $entity->status == 'active' ) { #unassign date will be end of month
                        $unassign_date = $end->copy();
                    }

                    # Now we just find total working days by subtracting assign_date and unassign_date +1 for adding first day
                    $usage_days = $unassign_date->diffInDays($assign_date) + 1;

                    $unassign_in_middle = false;
                    # Find unassign_date diff from month end
                    # if sim unassigned in middle of month, charge full bill
                    // $unassign_in_middle = false;
                    // if($unassign_date){
                    //     $unassign_date_diff = $end->diffInDays($unassign_date);
                    //     if($unassign_date_diff > 0){
                    //         $usage_days = $days_in_month;

                    //         $unassign_in_middle = true;
                    //     }
                    // }

                    // # if sim assign in middle of month, charge full bill
                    // if($assign_date){
                    //     $assign_date_diff = $assign_date->diffInDays($start);
                    //     if($assign_date_diff > 0){
                    //         $usage_days = $days_in_month;

                    //         $unassign_in_middle = true;
                    //     }
                    // }


                    # How much bill this entity will have
                    $splitted_bill = round( ( $usage_days / $days_in_month ) * $basePayload->amount, 2);
                    $working_allowed_balance = round( ( $usage_days / $days_in_month ) * $allowed_balance, 2);
                    $usage_amount = round($splitted_bill - $working_allowed_balance,2);

                    # If extra usage is negetive, make it 0
                    if($usage_amount<0)$usage_amount=0;

                    return (object) array_merge((array) $basePayload, (array) [
                        'entity' => $entity,
                        'source' => $entity->source->toArray(),
                        'unassign_date' => $unassign_date->format('Y-m-d'),
                        'assign_date' => $assign_date->format('Y-m-d'),
                        'usage_unassign_in_middle' => $unassign_in_middle, // We charged full amount if unassigned in middle on month
                        'usage_days' => $usage_days,
                        'usage_base' => $splitted_bill, // total bill according to usage
                        'usage_allowed' => $working_allowed_balance, // amount that are paid by company
                        'usage_amount' => $usage_amount, // amount that will be charged from source {base - allowed}
                    ]);

                });

                return $entities;

            }

        });

        // dd($rows);

        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        $this->_IH_init("simbills");
        $this->_IH_addData([
            'total_records' => $rows->count()
        ]);

        $total_amount = round($total_amount, 2);

        $baseTitle = 'Sim Bill ( '.$total_count.' )';

        $description = $month->format('M Y');

        $transactionLedger = new TransactionLedger;
        $transactionLedger->title = $baseTitle;
        $transactionLedger->given_date = Carbon::now()->format('Y-m-d');
        $transactionLedger->month = $month->format('Y-m-d');
        $transactionLedger->amount = $total_amount;
        $transactionLedger->description = $description;
        $transactionLedger->by = Auth::user()->id;
        $transactionLedger->tag = "sim_bill";
        $transactionLedger->save();


        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$transactionLedger->id;
        $ledger->source_model=get_class($transactionLedger);
        $ledger->date=$transactionLedger->given_date;
        $ledger->tag="sim_bill";
        $ledger->month = $transactionLedger->month; // For Filteration Purpose
        $ledger->is_cash= $paid_amount > 0 ? true : false;
        $ledger->amount = $paid_amount > 0 ? $paid_amount : $total_amount;
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
        $relation->tag = 'sim_bill';
        $relation->is_real = true;
        $relation->save();

        $transaction_payload = [
            'account_id' => $account->id,
            'type'=>"dr",
            'date' => $transactionLedger->given_date,
            'title'=>$transactionLedger->title,
            'description'=>$transactionLedger->description,
            'tag'=>'sim_bill',
            'status' => "pending",
            'real_amount'=>$total_amount,
            'additional_details' => [
                "tl_id" => $transactionLedger->id,
                "is_cheque" => 0,
                'charge_date' => $transactionLedger->given_date
            ],
            'links'=>[
                [
                    'modal'=>get_class(new TransactionLedger),
                    'id'=>$transactionLedger->id,
                    'tag'=>'sim_bill'
                ],
                [
                    'modal'=>get_class(new Ledger),
                    'id'=>$ledger->id,
                    'tag'=>'ledger'
                ]
            ]
        ];

        if($paid_amount > 0){
            $transaction_payload['status'] = "paid";
            $transaction_payload['amount'] = $paid_amount;
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


        # ------------------------------------
        #   Charge Simbills - Add to bookings
        # ------------------------------------
        foreach ($rows as $row) {

            $total_amount = round($row->amount, 2);
            $total_base = round($row->base, 2);
            $usage_amount = round($row->usage_amount, 2);
            $usage_base = round($row->usage_base, 2);
            $usage_allowed = round($row->usage_allowed, 2);
            $usage_days = $row->usage_days;
            $usage_unassign_in_middle = $row->usage_unassign_in_middle;

            $entity= $row->entity;
            $assign_date = Carbon::parse($row->assign_date);
            $unassign_date = Carbon::parse($row->unassign_date);
            $sim_number = $row->sim_number;
            $description= $row->description;


            $simNumber_title = 'Sim';

            $full_description = trim("$simNumber_title: $sim_number".(isset($row->base) ? "
            Basic Amount: $total_base" : "")."".(isset($row->amount) ? "
            Total Amount: $total_amount" : "")."".(isset($row->usage_days) ? "
            [Usage] Days: $usage_days (".$assign_date->format('d M')." - ".$unassign_date->format('d M').")" : "")."".(isset($row->usage_base) ? "
            [Usage] Basic: $usage_base ". ($usage_unassign_in_middle ? "[Unassigned in middle]" : "") ."" : "")."".(isset($row->usage_allowed) ? "
            [Usage] Allowed: $usage_allowed" : "")."".(isset($row->usage_amount) ? "
            [Usage] Amount: $usage_amount" : "")."

            $description");

            # ------------------
            #   Charge Bill
            # ------------------


            // :OLD
            // $charged_description = trim("$simNumber_title: $sim_number".(isset($row->usage_days) ? "
            // Days in use: $usage_days" : "")."".(isset($row->usage_base) ? "
            // Total Amount: $usage_base" : "")."".(isset($row->usage_allowed) ? "
            // Paid by company: $usage_allowed" : "")."".(isset($row->usage_amount) ? "
            // Charged Amount: $usage_amount" : "")."

            // $description
            // ");

            $charged_description = trim("$simNumber_title: $sim_number
            $description
            ");

            if($entity->source_model == User::class){

                $source = $entity->source;

                $resource_id = $source->id;

                if($usage_amount != 0){

                    # --------------------------
                    #   Add Employee Ledger
                    # --------------------------

                    #Add advance to Employee Ledger
                    $vLedgerItem = new Employee_ledger;
                    $vLedgerItem->type='dr';
                    $vLedgerItem->tag='sim_bill';
                    $vLedgerItem->title="Sim bill charged";
                    $vLedgerItem->description=$charged_description;
                    $vLedgerItem->month=$month;
                    $vLedgerItem->date=$end->format('Y-m-d');
                    $vLedgerItem->user_id=$resource_id;
                    $vLedgerItem->is_cash=false;
                    $vLedgerItem->amount=$usage_amount;

                    $vLedgerItem->save();

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'employee_ledger';
                    $relation->is_real = false;
                    $relation->save();

                }

            }
            else{

                $resource_id = null;
                $group = 'sim'.$sim_number;

                $source = $entity->source;
                if($entity->source_model == Driver::class){
                    $resource_id = $source->id;
                    $group .= '_driver'.$resource_id;
                }

                if(!isset($resource_id)){
                    throw ValidationException::withMessages(["1" => "Source not assigned to booking - SIM ".$sim_number]);
                    return;
                }

                $resource_id = (int) $resource_id;

                if($usage_amount != 0){

                    # ------------------------
                    # Add Statement Ledger Item
                    # : TO => DRIVER ACCOUNT
                    # ------------------------
                    
                    $vLedger = new StatementLedger;
                    $exists = StatementLedger::ofNamespace('driver', $resource_id)->first();;
                    if(isset($exists)) $vLedger = $exists;
                    else{
                        $vLedger->linked_to = 'driver';
                        $vLedger->linked_id = $resource_id;
                        $vLedger->save();
                    }


                    $vItemObj = (object)[
                        'title' => "Sim bill charged",
                        'description' => $charged_description,
                        'type' => "dr",
                        'group' => $group,
                        // 'tag' => strtolower( Str::slug( preg_replace('/\s+/', '', $sim_number) ) ) . "_simbill",
                        'tag' => "sim_bill",
                        'channel' => "import",
                        'date' => $end->format('Y-m-d'),
                        'month' => $month->format('Y-m-d'),
                        'amount' => $usage_amount,
                        'additional_details' => [
                            'total_amount' => $total_amount,
                            'total_base' => $total_base,
                            'usage_amount' => $usage_amount,
                            'assign_date' => $assign_date->format('Y-m-d'),
                            'unassign_date' => $unassign_date->format('Y-m-d'),
                            'usage_base' => $usage_base,
                            'usage_allowed' => $usage_allowed,
                            'usage_days' => $usage_days,
                            'usage_unassign_in_middle' => $usage_unassign_in_middle
                        ]
                    ];

                    $vItemObj->attachment = null;

                    $vLedgerItem =  $vLedger->addItem($vItemObj);

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'simbill_statementledger_driver';
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

                    // Flow:
                    //  cr: usage_amount that deducts from rider
                    //  dr: usage_base that we will pay
                    $vItemObj->title = 'Sim bill (Charged)';
                    $vItemObj->type = 'cr'; // Credit the amount
                    $vItemObj->amount = $usage_amount; // Total amount according to usage
                    
                    $vLedgerItem = $vLedger->addItem($vItemObj);


                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'simbill_statementledger_company';
                    $relation->is_real = false;
                    $relation->save();


                    $vItemObj->title = 'Sim bill (Paid)';
                    $vItemObj->type = 'dr'; // Debit the amount
                    $vItemObj->amount = $usage_base; // Total amount according to usage
                    
                    $vLedgerItem =  $vLedger->addItem($vItemObj);

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'simbill_statementledger_company';
                    $relation->is_real = false;
                    $relation->save();

                }

            }


            # -----------------------
            #   Insert Record
            # -----------------------
            $transaction_detail = new TransactionLedgerDetails;
            $transaction_detail->tl_id = $transactionLedger->id;
            $transaction_detail->source_id = $source->id;
            $transaction_detail->source_model = get_class($source);
            $transaction_detail->description = $full_description;
            $transaction_detail->amount = $usage_amount;
            $transaction_detail->additional_details = [
                'sim_number' => $sim_number,
                'total_amount' => $total_amount,
                'total_base' => $total_base,
                'assign_date' => $assign_date->format('Y-m-d'),
                'unassign_date' => $unassign_date->format('Y-m-d'),
                'usage_amount' => $usage_amount,
                'usage_base' => $usage_base,
                'usage_allowed' => $usage_allowed,
                'usage_days' => $usage_days,
                'usage_unassign_in_middle' => $usage_unassign_in_middle
            ];
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
