<?php

namespace App\Imports;

use App\Accounts\Models\Account;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\StatementLedger;
use App\Models\Tenant\StatementLedgerItem;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\Vehicle;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\VehicleLedgerItem;
use App\Traits\ImportHistoryTrait;
use Carbon\Carbon;
use Exception;
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

class StatementLedgerItemsImport implements ToCollection, WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{

    use RemembersChunkOffset, ImportHistoryTrait;

    private $totalRows;
    private $importedRows;

    public function __construct()
    {
        $this->totalRows = 0;
        $this->importedRows = 0;


    }

    /**
    * @param Illuminate\Support\Collection $rows
    *
    * @return null
    */
    public function collection(Collection $rows): void
    {
        // dd($rows->first()->toArray());
        # Perform basic header validation on 1st row
        $errorFound = $this->headersValidation([
            'driverid',
            'month',
            'givendate',
            'title',
            'description',
            'action',
            'amount',
            'attachment',
            'account',
        ], $rows->first()->toArray());

        if(!$errorFound){



            $accountHandles = $rows
            ->pluck('account')
            ->unique()
            ->filter(function ($value, $key) {
                return isset($value) && $value !== '';
            })
            ->values();

            $accounts = Account::whereIn('handle', $accountHandles)->get();

            # validate accounts
            foreach ($rows->groupBy('account') as $account_handle => $account_rows) {

                if(isset($account_handle) && $account_handle !== ''){


                    $account = $accounts->where('handle', $account_handle)->first();
                    if(!isset($account)){
                        throw ValidationException::withMessages(["1" => "Account not found against ".$account_handle]);
                        return;
                    }


                    $cr = $account_rows
                    ->where('action', 'cr')
                    ->sum('amount');
                    $dr = $account_rows
                    ->where('action', 'dr')
                    ->sum('amount');

                    $totalAmount = round(($cr-$dr), 2);

                    // dump("B:".$totalAmount.' A:'.$account->balance.' C: '.$account->title.' D:'.$account_handle);


                    if(!app('helper_service')->routes->has_custom_access('negative_account_balance', [$account->id]) && $totalAmount < 0 && $account->balance < abs($totalAmount) ){
                        throw ValidationException::withMessages(["1" => "Insufficient balance in <b>".$account->title.":</b> Deduction is ".abs($totalAmount)." and remaining balance is ".$account->balance]);
                        return;
                    }
                }

            }


            $this->_IH_init("statement_ledger");
            $this->_IH_addData([
                'total_records' => $rows->count()
            ]);

            $errors = [];

            foreach ($rows as $rowIndex => $row) {

                # -------------
                #   Payload
                # -------------
                $rowNo = $rowIndex + 2; # +1 beacause index start at zero, +1 because we have header row

                $amount = (float)str_replace(',', '', $row['amount']);
                $account_handle= $row['account'];
                $driver_id = (int)trim($row['driverid']);
                $date=Carbon::createFromFormat("d/m/Y", $row['givendate'])->format('Y-m-d');
                $month=Carbon::createFromFormat("d/m/Y", $row['month'])->startOfMonth()->format('Y-m-d');
                $title=$row['title'];
                $tag=$row['tag'] ?? null;
                $title=$row['title'];
                $description=$row['description'];
                $action=$row['action'];
                $attachment=$row['attachment'];

                $account = null;

                if(isset($account_handle) && $account_handle !== ''){
                    $account = $accounts->where('handle', $account_handle)->first();
                }

                # --------------------------
                #   Save Statement Ledger 
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
                    'title' => trim($title),
                    'description' => trim($description),
                    'type' => trim($action),
                    'tag' => isset($tag) && $tag !== '' ? strtolower(trim($tag)) : 'manual_transaction',
                    'date' => $date,
                    'month' => $month,
                    'amount' => $amount,
                    'channel' => 'import'
                ];

                # need to check if image added
                if(isset($attachment) && $attachment !== ""){
                    $contents = file_get_contents($attachment);
                    $filepath = 'vehicle_ledgers/imports/' . substr($attachment, strrpos($attachment, '/') + 1);
                    Storage::put($filepath, $contents);
                    $vItemObj->attachment = $filepath;
                }
                $vLedgerItem = $vLedger->addItem($vItemObj);

                if(isset($attachment) && $attachment !== "" && isset($vLedgerItem->attachment)){
                    $vItemObj->attachment = Storage::url($vLedgerItem->attachment);
                }

                # ------------------------------------
                # Save Ledger + Account Transaction
                # ------------------------------------
                if(isset($account)){

                    $prefix = null;

                    # Save ledger
                    $ledger = new Ledger;
                    $ledger->type=$action;
                    $ledger->source_id=$vLedgerItem->_id;
                    $ledger->source_model=get_class($vLedgerItem);
                    $ledger->date=$date;
                    $ledger->tag="statementledger_transaction";
                    $ledger->month = $month; // For Filteration Purpose
                    $ledger->is_cash=true;
                    $ledger->amount=$amount;
                    $ledger->props=[
                        'by'=>Auth::user()->id,
                        'import' => true,
                        'prefix' => $prefix,
                        'account'=>[
                            'id'=>$account->_id,
                            'title'=>$account->title
                        ]
                    ];
                    $ledger->save();


                    $this->_IH_addRecord(get_class($ledger), $ledger->id);


                    #create account transaction
                    $transaction = \App\Accounts\Handlers\AccountGateway::add_transaction([
                        'account_id' => $account->id,
                        'type'=>$action,
                        'date' => $date,
                        'title'=>"Statement Ledger | " . $vLedgerItem->title,
                        'description'=>$vLedgerItem->description.' | '.Carbon::parse($month)->format('M Y'),
                        'tag'=>'statementledger_transaction',
                        'amount'=>$amount,
                        'additional_details' => [
                            "driver_id" => $vLedger->linked_id,
                            'attachment' => isset($vItemObj->attachment) ? $vItemObj->attachment : null
                        ],
                        'links'=>[
                            [
                                'modal'=>get_class(new StatementLedgerItem),
                                'id'=>$vLedgerItem->_id,
                                'tag'=>'statementledger_transaction'
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

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'statementledger_transaction';
                    $relation->is_real = true;
                    $relation->save();
                }
                else{
                    # Just add the transaction


                    $prefix = null;

                    # Save ledger
                    $ledger = new Ledger;
                    $ledger->type=$action;
                    $ledger->source_id=$vLedgerItem->_id;
                    $ledger->source_model=get_class($vLedgerItem);
                    $ledger->date=$date;
                    $ledger->tag="statementledger_transaction";
                    $ledger->month = $month; // For Filteration Purpose
                    $ledger->is_cash=false;
                    $ledger->amount=$amount;
                    $ledger->props=[
                        'by'=>Auth::user()->id,
                        'prefix' => $prefix,
                        'import' => true
                    ];
                    $ledger->save();

                    $this->_IH_addRecord(get_class($ledger), $ledger->id);

                    #add relations
                    $relation = new Table_relation;
                    $relation->ledger_id = $ledger->id;
                    $relation->source_id = $vLedgerItem->_id;
                    $relation->source_model = get_class($vLedgerItem);
                    $relation->tag = 'statementledger_transaction';
                    $relation->is_real = true;
                    $relation->save();

                }
            }

            $this->_IH_end();


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


    public function rules(): array
    {
        return [
            'driverid' => 'required|max:255',
            'month' => 'required|date_format:d/m/Y',
            'givendate' => 'required|date_format:d/m/Y',
            'title' => 'required|max:255',
            'description' => 'nullable|max:255',
            'amount' => 'required|gt:0',
            'action' => 'required|in:cr,dr',
            'attachment' => 'nullable|url',
            'account' => 'nullable|max:255',
        ];
    }

}
