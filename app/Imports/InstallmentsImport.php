<?php

namespace App\Imports;

use App\Accounts\Models\Account;
use App\Models\Tenant\Addon;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Installment;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Table_relation;
use App\Models\Tenant\TransactionLedger;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithEvents;

class InstallmentsImport implements ToCollection, WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows
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
        if($rows->count() == 0) return;
        # Perform basic header validation on 1st row
        $errorFound = $this->headersValidation([
            'chargedate',
            'paydate',
            'chargeamount',
            'payamount',
            'source',
            'sourceid',
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
                }

            }

            $this->_IH_init("installment");
            $this->_IH_addData([
                'total_records' => $rows->count()
            ]);

            # Generate unique code, like 1000.1,1000.2
            $seq = DB::getCollection('counters')->findOneAndUpdate(
                ['ref' => 'installment_scheduler'],
                ['$inc' => ['seq' => 1]],
                ['new' => true, 'upsert' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
            );
            # seq will be used as code
            $code = $seq->seq;


            # ---------------------
            #   Save Installments
            # ---------------------
            foreach ($rows as $row) {

                $charge_amount = (float)$row['chargeamount'];
                $pay_amount = (float)$row['payamount'];
                $account_handle= $row['account'];
                $source_id= (int) $row['sourceid'];
                $source_type= $row['source'];
                $charge_date=Carbon::parse($row['chargedate']);
                $pay_date=Carbon::parse($row['paydate']);

                $account = null;

                if(isset($account_handle) && $account_handle !== ''){
                    $account = $accounts->where('handle', $account_handle)->first();
                }

                $source_model = '';
                switch ($source_type) {
                    case 'vehicle':
                        $source_model = get_class(new Vehicle);
                        break;
                    case 'addon':
                        $source_model = get_class(new Addon);
                        break;
                    case 'driver':
                        $source_model = get_class(new Driver);
                        break;
                    case 'booking':
                        $source_model = get_class(new VehicleBooking);
                        break;

                    default:
                        # code...
                        break;
                }

                $installment = new Installment;
                $installment->charge_date = $charge_date;
                $installment->pay_date = $pay_date;
                $installment->charge_amount = $charge_amount;
                $installment->pay_amount = $pay_amount;
                $installment->source_id = $source_id;
                $installment->source_model = $source_model;
                $installment->account_id = $account->id;
                $installment->code = $code;
                $installment->by = Auth::user()->id;
                $installment->transaction_ledger_id = null;
                $installment->save();

                $this->_IH_addRecord(get_class($installment), $installment->id);

            }

            $this->_IH_end();

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
        return 1500;
    }


    public function rules(): array
    {
        return [
            'chargedate' => 'required|date',
            'paydate' => 'required|date',
            'chargeamount' => 'required|numeric',
            'payamount' => 'required|numeric',
            'source' => 'required|in:vehicle,addon,driver,booking',
            'sourceid' => 'required|numeric',
            'account' => 'nullable|max:255',
        ];
    }

}
