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
use App\Models\Tenant\VehicleBillsCharge;
use App\Models\Tenant\VehicleBillsDetail;
use App\Models\Tenant\VehicleBillsSetting;
use App\Models\Tenant\VehicleBillsSpend;
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

class VehicleBillsImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows
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
        $bill_setting = VehicleBillsSetting::findOrFail($request->bill_setting_id);

        $month = Carbon::parse($request->month)->startOfMonth();
        $received_amount = (float) $request->get('received_amount', 0);

        $errors = [];

        
        if(!isset($bill_setting)){
            $errors["Bill"] = "Bill setting was not found";
        }

        
        // Fetch vehicles with assign histories
        $vehicles = Vehicle::with([
            'entities' => function($query) use ($month){

                # Match the range and fetch riders accordingly

                # Bind 2 where into 1
                $query->where(function ($q) use ($month) {
                    # This where will filter histories with "active" entities
                    $q->where(function ($q2) use ($month) {
                        $q2->where('assign_date', '<=', $month->copy()->endOfMonth())
                        ->whereNull('unassign_date');
                    })
                    # This where will filter histories with "inactive" entities
                    ->orWhere(function ($q2) use ($month) {
                        $q2->where('assign_date', '<=', $month->copy()->endOfMonth())
                        ->where('unassign_date', '>=', $month)
                        ->whereNotNull('unassign_date');
                    });
                })
                ->where('source_model', Driver::class);
            },
            'entities.source'
        ])
        ->get();


        # -----------------------------
        # Map with selected headers
        # -----------------------------
        $rows = $rows->map(function($row, $rowIndex) use ($mapper, $bill_setting, $vehicles, &$errors, $month){

            $rowNumber = $rowIndex + 2;

            $plate = null;
            $date = null;
            $ref = null;
            $charge_amount = null;
            $spend_amount = null;
            $descriptions = [];

            $uuid = app('helper_service')->helper->generateUniqueId(10);

            foreach ($row as $index => $value) {
                $mapItem = $mapper->where('source', $index)->first();
                if(isset($mapItem)){
                    $destination = $mapItem['destination'];
                    $title = $mapItem['title'];

                    if($destination === "plate" && !isset($plate)){
                        $plate = $value;
                    }
                    if($destination === "date" && !isset($date)){
                        $date = Carbon::parse($value)->format('Y-m-d');
                    }
                    if($destination === "uuid" && !isset($ref)){
                        $ref = $value;
                    }
                    if($destination === "charged_amount" && !isset($charge_amount)){
                        $charge_amount = app('helper_service')->helper->parseNumber($value);
                    }
                    if($destination === "spend_amount" && !isset($spend_amount)){
                        $spend_amount = app('helper_service')->helper->parseNumber($value);
                    }
                    if($destination === "description"){
                        if(isset($value) && trim($value)!='') $descriptions[] = "$title: $value";
                    }

                }
            }

            // Check if spend_amount not found
            if(!isset($spend_amount) && $bill_setting->charged_is_spend === true){
                $spend_amount = $charge_amount;
            }

            $plateCode = null;
            if($bill_setting->includes_platecode === true && str_contains($plate, '-')){
                // Plate has platecode included like "12345-1"
                // Split at "-" and get the 1st part
                $newPlate = explode("-", $plate);
                $plate = $newPlate[0];
                $plateCode = $newPlate[1];

            }

            // Find vehicle against plate
            $vehicle = $vehicles->where('plate', $plate);

            if(isset($plateCode)){
                $vehicle = $vehicles->where('plate_code', $plateCode);
            }

            $drivers = collect([]);
            $entities = collect([]);
            $vehicle = $vehicle->first();

            if(!isset($vehicle)){
                $errors[] = "row#$rowNumber - Vehicle not found against $plate";
            }
            else{

                // Check if spend amount is still not found
                // Check if this is "Rent". We need to pick amount from vehicle active client
                $bt = strtolower( Str::slug( preg_replace('/\s+/', '', $bill_setting->title) ) );
                if($bt==='bikerent'){
                    // TODO:: PICK VEHICLE CLIENT
                }

                // Find driver against vehicle
                // if logic is "no_split" we will use date otherwise find all drivers in current month

                // Map entities so we have clear dates
                $entities = $vehicle->entities
                ->map(function($entity) use ($month){

                    # Get no of days rider had this vehicle
                    $assign_date = Carbon::parse($entity->assign_date);
                    $unassign_date = $entity->status == 'inactive' ? Carbon::parse($entity->unassign_date) : null;
                    if ($assign_date->lessThan($month)) { #assign date will be start of month
                        $assign_date = $month->copy();
                    }
                    if ( (isset($unassign_date) && Carbon::parse($entity->unassign_date)->greaterThan($month->copy()->endOfMonth())) ) { #unassign date will be end of month
                        $unassign_date = $month->copy()->endOfMonth();
                    }
                    else if ($entity->status == 'active'){
                        // Since unassign date is null, we add 5 months to unassign date just in case 
                        $unassign_date = $month->copy()->endOfMonth();
                        
                    }

                    # Now we just find total working days by subtracting assign_date and unassign_date +1 for adding first day
                    $usage_days = $unassign_date->diffInDays($assign_date) + 1;

                    $entity->calc_unassign_date = $unassign_date;
                    $entity->calc_assign_date = $assign_date;
                    $entity->usage_days = $usage_days;

                    return $entity;
                
                });

                $drivers = $entities
                ->filter(function($item) use ($bill_setting, $date){
                    if($bill_setting->logic === "no_split"){
                        // Find entity fall in $date, i.e. $date >= assign_date && $date <= unassign_date
                        if($item->status === 'active'){
                            // Match only assign date
                            return Carbon::parse($date)->greaterThanOrEqualTo($item->assign_date);

                        }
                        return Carbon::parse($date)->greaterThanOrEqualTo($item->assign_date) && Carbon::parse($date)->lessThanOrEqualTo($item->calc_unassign_date);
                    }

                    return true;

                })
                ->map(function($item) use ($month, $charge_amount, $bill_setting) {
                    $splitted_bill = round( ( $item->usage_days / $month->daysInMonth ) * $charge_amount, 2);

                    if($bill_setting->logic === "no_split"){
                        // Whole amount because vehicle was assigned to this driver on mentioned date
                        $splitted_bill = $charge_amount;
                    }

                    return (object)[
                        'id' => $item->source->id,
                        'name' => $item->source->name,
                        'usage' => $item->usage_days,
                        'amount' => $splitted_bill
                    ];
                });

                // Check if driver count is > 1 and logic in no_split
                // it means 2 driver works on same date, that is conflict, we need to generate error
                if($bill_setting->logic === "no_split" && $drivers->count() > 1){
                    $errors[] = "row#$rowNumber - Driver conflict! Date: $date, Drivers: " . $drivers->map(fn($driver) => "KL" . $driver->id)->implode(" / ");
                }
                
            }

            
            
            return (object)[
                'rowNumber' => $rowNumber,
                'plate' => $plate,
                'plate_code' => $plateCode,
                'vehicle_plate' => $vehicle->plate,
                'vehicle_plate_code' => $vehicle->plate_code,
                'vehicle_id' => $vehicle->id,
                'drivers' => $drivers,
                'date' => $date,
                'ref' => $ref,
                'uuid' => $uuid,
                'charge_amount' => isset($charge_amount) ? (float)$charge_amount : 0,
                'spend_amount' => isset($spend_amount) ? (float)$spend_amount : 0,
                'description' => implode("<br />", $descriptions),
            ];

        });

        // Check for duplication
        $refs = $rows->pluck('ref')->filter(fn($item) => isset($item))->values();
        $duplicate_records = VehicleBillsDetail::whereIn('ref', $refs)
        ->get();
        if($duplicate_records->count() > 0){
            // Append errors with row number
            foreach ($duplicate_records as $item) {
                $targetRow = $rows->firstWhere('ref', $item->ref);
                if(isset($targetRow)){
                    $errors[] = "Duplicate UUID found \"$item->ref\" at Row#$targetRow->rowNumber";
                }
            }
        }

        // -----------------------------------------------------
        // If grouped, we need to make groups of similer rows
        // -----------------------------------------------------

        $detail_rows = collect([]);
        $spend_rows = collect([]);
        $charge_rows = collect([]);

        foreach ($rows as $row) {
            $charge_amount = round($row->charge_amount, 2);
            $spend_amount = round($row->spend_amount, 2);

            $clonned_row = clone $row;
            unset($clonned_row->drivers);

            $uuid = app('helper_service')->helper->generateUniqueId(10);

            $clonned_row->uuid = $uuid;

            // SPEND ROWS --> Unalize spend row first, it will act as master entry
            // All the charged/details row will be linked with this entry for future references
            if($bill_setting->grouped === true){
                // Group by vehicle and push them

                $spendRowIndex = $spend_rows->search(function ($item) use ($row) {
                    return $item->vehicle_id === $row->vehicle_id;
                });

                if($spendRowIndex !== false){
                    $spend_rows[$spendRowIndex]->spend_amount += $spend_amount;

                    // Update UUID
                    $clonned_row->uuid = $spend_rows[$spendRowIndex]->uuid;

                    // Push Row
                    $spend_rows[$spendRowIndex]->data[] = (array)$clonned_row;
                }
                else{
                    $spend_rows->push((object)[
                        'spend_amount' => $clonned_row->spend_amount,
                        'uuid' => $clonned_row->uuid,
                        'vehicle_id' => $clonned_row->vehicle_id,
                        'vehicle_plate' => $clonned_row->vehicle_plate,
                        'data' => [(array)$clonned_row]
                    ]);
                }


            }
            else{
                
                $spend_rows->push((object)[
                    'spend_amount' => $clonned_row->spend_amount,
                    'uuid' => $clonned_row->uuid,
                    'vehicle_id' => $clonned_row->vehicle_id,
                    'vehicle_plate' => $clonned_row->vehicle_plate,
                    'data' => [(array)$clonned_row]
                ]);
            }

            // CHARGE ROWS --> Loop through driver to charge and split the bill based on usage days
            foreach ($row->drivers as $driver) {
                
                if($bill_setting->grouped === true){
                    // Group by driver + vehicle and push them

                    $chargeRowIndex = $charge_rows->search(function ($item) use ($driver, $row) {
                        return $item->driver_id === $driver->id && $item->vehicle_id === $row->vehicle_id;
                    });

                    if($chargeRowIndex !== false){
                        $charge_rows[$chargeRowIndex]->charge_amount += $driver->amount;

                        // Push Row
                        $charge_rows[$chargeRowIndex]->data[] = (array)$clonned_row;
                    }
                    else{
                        $charge_rows->push((object)[
                            'charge_amount' => $driver->amount,
                            'uuid' => $clonned_row->uuid,
                            'vehicle_id' => $clonned_row->vehicle_id,
                            'vehicle_plate' => $clonned_row->vehicle_plate,
                            'driver_id' => $driver->id,
                            'driver_name' => $driver->name,
                            'usage' => $driver->usage,
                            'data' => [(array)$clonned_row]
                        ]);
                    }

                }
                else{
                    
                    $charge_rows->push((object)[
                        'charge_amount' => $driver->amount,
                        'uuid' => $clonned_row->uuid,
                        'vehicle_id' => $clonned_row->vehicle_id,
                        'vehicle_plate' => $clonned_row->vehicle_plate,
                        'driver_id' => $driver->id,
                        'driver_name' => $driver->name,
                        'usage' => $driver->usage,
                        'data' => [(array)$clonned_row]
                    ]);
                }

            }

            // DETAIL ROW --> Add each row in detail row
            $detail_rows->push((object)[
                ...(array)$clonned_row
            ]);



        }



        // $tmp = [
        //     'details' => $detail_rows,
        //     'spend' => $spend_rows,
        //     'charge' => $charge_rows,

        // ];
        // if(count($errors) > 0) dd($errors);
        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode($tmp);
        // die;
        // dd();
        // return;


        if(count($errors) > 0){
            throw ValidationException::withMessages($errors);
            return;
        }

        # ------------------------
        # IMPORT STARTS FROM HERE
        #  VARIABLES:
        #   $detail_rows = Each records same as imported
        #   $spend_rows = Group by vehicle amounts
        #   $charge_rows = Group by vehicle + drivers amounts (based on usage)
        # ------------------------
        

        $this->_IH_init("vehiclebills");
        $this->_IH_addData([
            'total_records' => $rows->count()
        ]);


        $total_spend = round($detail_rows->whereNotNull('spend_amount')->sum('spend_amount'), 2);
        $total_charged = round($detail_rows->whereNotNull('charge_amount')->sum('charge_amount'), 2);
        $date = Carbon::now()->format('Y-m-d');
        $baseTitle = "$bill_setting->title";

        $description = trim("".($detail_rows->whereNotNull('spend_amount')->count() > 0 ? "Spend: $total_spend" : "")."
        ".($detail_rows->whereNotNull('charge_amount')->count() > 0 ? "Charged: $total_charged" : "")."
        Month: {$month->copy()->format('M Y')}
        ");


        $transaction_payload = [
            'account_id' => $account->id,
            'type'=>"dr",
            'date' => $date,
            'title'=>$baseTitle . " ({$detail_rows->count()} records)",
            'description'=>$description,
            'tag'=>'vehiclebills',
            'status' => "pending",
            'real_amount'=>$total_spend,
            'additional_details' => [
                "bill_setting_id" => $bill_setting->id,
                "is_cheque" => 0
            ],
            'links'=>[ ] // Since this will act as master entry, we will link entries later
        ];

        if($received_amount > 0){
            $transaction_payload['status'] = "paid";
            $transaction_payload['amount'] = $received_amount;
        }

        # Create account transaction
        $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction($transaction_payload);

        # Save ledger
        $ledger = new Ledger;
        $ledger->type="dr";
        $ledger->source_id=$transaction->id;
        $ledger->source_model=get_class($transaction);
        $ledger->date=$date;
        $ledger->tag="vehiclebills";
        $ledger->month = $month->copy()->format('Y-m-d'); // For Filteration Purpose
        $ledger->is_cash= $received_amount > 0 ? true : false;
        $ledger->amount = $received_amount > 0 ? $received_amount : $total_spend;
        $ledger->props=[
            'by'=>Auth::user()->id,
            'import' => true,
            'account'=>[
                'id'=>$account->id,
                'title'=>$account->title
            ]
        ];
        $ledger->save();

        $this->_IH_addRecord(get_class($ledger), $ledger->id);

        #add relations
        $relation = new Table_relation;
        $relation->ledger_id = $ledger->id;
        $relation->source_id = $transaction->id;
        $relation->source_model = get_class($transaction);
        $relation->tag = 'transaction';
        $relation->is_real = true;
        $relation->save();

        \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
            'transaction_id' => $transaction->id,
            'modal_class'=>get_class($ledger),
            'modal_id'=>$ledger->id,
            'tag'=>'ledger'
        ]);


        # --------------------------------
        #   OF: $spend_rows
        #   Table: vehicle_bills_spends
        # --------------------------------
        foreach ($spend_rows as $row) {

            # --------------------------
            #   Payload
            # --------------------------
            $amount = round($row->spend_amount, 2);
            $uuid = $row->uuid;
            $vehicle_id = $row->vehicle_id;
            $vehicle_plate= $row->vehicle_plate;

            // Find charged records against this
            $charged_to_count = $charge_rows->where('uuid', $uuid)->count();

            $full_description = trim("
            Plate: $vehicle_plate
            Charged Count: $charged_to_count
            ");

            $refs = collect($row->data)->pluck('ref')->unique()->filter(fn($item) => isset($item))->values()->toArray();

            # -----------------
            #   Add Bill Item
            # -----------------
            $item = new VehicleBillsSpend;
            $item->bill_setting_id = $bill_setting->id;
            $item->vehicle_id = $vehicle_id;
            $item->date = $date;
            $item->month = $month->copy()->format('Y-m-d');
            $item->uuid = $uuid;
            $item->amount = $amount;
            $item->refs = $refs;
            $item->description = $full_description;
            $item->save();

            # -----------------
            #   Add Relations
            # -----------------

            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $item->id;
            $relation->source_model = get_class($item);
            $relation->tag = 'vehiclebillspend';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($item),
                'modal_id'=>$item->id,
                'tag'=>'vehiclebillspend'
            ]);

        }

        # --------------------------------
        #   OF: $charge_rows
        #   Table: vehicle_bills_charged
        # --------------------------------
        foreach ($charge_rows as $row) {

            # --------------------------
            #   Payload
            # --------------------------
            $amount = round($row->charge_amount, 2);
            $uuid = $row->uuid;
            $vehicle_id = $row->vehicle_id;
            $vehicle_plate = $row->vehicle_plate;
            $driver_id = (int)$row->driver_id;
            $usage = $row->usage;

            // get the first date of records
            $firstDate = count($row->data) > 0 ? $row->data[0]['date'] : $date;
            $totalSpendAmount = collect($row->data)->sum('spend_amount');

            $refs = collect($row->data)->pluck('ref')->unique()->filter(fn($item) => isset($item))->values()->toArray();

            $full_description = trim("
            Plate: $vehicle_plate
            Usage Days: $usage
            ");

            # -----------------
            #   Add Bill Item
            # -----------------
            $item = new VehicleBillsCharge;
            $item->bill_setting_id = $bill_setting->id;
            $item->vehicle_id = $vehicle_id;
            $item->driver_id = $driver_id;
            $item->date = $firstDate;
            $item->month = $month->copy()->format('Y-m-d');
            $item->uuid = $uuid;
            $item->refs = $refs;
            $item->amount = $amount;
            $item->description = $full_description;
            $item->save();

            # -----------------
            #   Add Relations
            # -----------------

            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $item->id;
            $relation->source_model = get_class($item);
            $relation->tag = 'vehiclebillcharge';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($item),
                'modal_id'=>$item->id,
                'tag'=>'vehiclebillcharge'
            ]);

            # --------------------------
            #   Add Vehicle Ledger Item
            #    : TO => DRIVER ACCOUNT
            # --------------------------
            
            
            $vLedger = new StatementLedger;
            $exists = StatementLedger::ofNamespace('driver', $driver_id)->first();;
            if(isset($exists)) $vLedger = $exists;
            else{

                $vLedger->linked_to = 'driver';
                $vLedger->linked_id = $driver_id;
                $vLedger->save();
            }

            $vItemObj = (object)[
                'title' => $baseTitle,
                'description' => $full_description,
                'type' => "dr",
                'tag' => strtolower( Str::slug( preg_replace('/\s+/', '', $bill_setting->title) ) ) . "_vehiclebills",
                'channel' => "import",
                'date' => $firstDate,
                'month' => $month->copy()->format('Y-m-d'),
                'amount' => $amount,
                'attachment' => null,
                'additional_details' => [
                    'uuid' => $uuid,
                    'bill_setting_id' => $bill_setting->id,
                    'vehicle_id' => $vehicle_id
                ]
            ];

            $vLedgerItem =  $vLedger->addItem($vItemObj);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'vehiclebill_statementledger_driver';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($vLedgerItem),
                'modal_id'=>$vLedgerItem->id,
                'tag'=>'vehiclebill_statementledger_driver'
            ]);


            # --------------------------
            #   Add Vehicle Ledger Item
            #    : TO => COMPANY ACCOUNT
            # --------------------------
            
            
            $vLedger = new StatementLedger;
            $exists = StatementLedger::ofNamespace('company', $driver_id)->first();;
            if(isset($exists)) $vLedger = $exists;
            else{

                $vLedger->linked_to = 'company';
                $vLedger->linked_id = $driver_id;
                $vLedger->save();
            }

            // Flow:
            //  cr: amount (Charged Amount) that deducts from rider
            //  dr: totalSpendAmount that we will pay
            
            $vItemObj->title = "$baseTitle (Paid)";
            $vItemObj->type = 'dr'; // Debit the amount
            $vItemObj->amount = $totalSpendAmount; // spend amount

            $vLedgerItem =  $vLedger->addItem($vItemObj);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'vehiclebill_statementledger_company';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($vLedgerItem),
                'modal_id'=>$vLedgerItem->id,
                'tag'=>'vehiclebill_statementledger_company'
            ]);



            
            $vItemObj->title = "$baseTitle (Charged)";
            $vItemObj->type = 'cr'; // Credit the amount
            $vItemObj->amount = $amount; // charge amount

            $vLedgerItem =  $vLedger->addItem($vItemObj);

            #add relations
            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $vLedgerItem->id;
            $relation->source_model = get_class($vLedgerItem);
            $relation->tag = 'vehiclebill_statementledger_company';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($vLedgerItem),
                'modal_id'=>$vLedgerItem->id,
                'tag'=>'vehiclebill_statementledger_company'
            ]);



        }

        
        # --------------------------------
        #   OF: $detail_rows
        #   Table: vehicle_bills_details
        # --------------------------------
        foreach ($detail_rows as $row) {

            # --------------------------
            #   Payload
            # --------------------------
            $spend_amount = round($row->spend_amount, 2);
            $charge_amount = round($row->charge_amount, 2);
            $uuid = $row->uuid;
            $description = $row->description;
            $ref = $row->ref;
            $rowDate = $row->date;
            $vehicle_id = $row->vehicle_id;
            $vehicle_plate= $row->vehicle_plate;

            # -----------------
            #   Add Bill Item
            # -----------------
            $item = new VehicleBillsDetail;
            $item->bill_setting_id = $bill_setting->id;
            $item->vehicle_id = $vehicle_id;
            $item->date = $rowDate;
            $item->month = $month->copy()->format('Y-m-d');
            $item->uuid = $uuid;
            $item->ref = $ref;
            $item->charge_amount = $charge_amount;
            $item->spend_amount = $spend_amount;
            $item->description = $description;
            $item->save();

            # -----------------
            #   Add Relations
            # -----------------

            $relation = new Table_relation;
            $relation->ledger_id = $ledger->id;
            $relation->source_id = $item->id;
            $relation->source_model = get_class($item);
            $relation->tag = 'vehiclebilldetail';
            $relation->is_real = false;
            $relation->save();

            \App\Accounts\Handlers\AccountGateway::attach_links_to_transaction([
                'transaction_id' => $transaction->id,
                'modal_class'=>get_class($item),
                'modal_id'=>$item->id,
                'tag'=>'vehiclebilldetail'
            ]);

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
