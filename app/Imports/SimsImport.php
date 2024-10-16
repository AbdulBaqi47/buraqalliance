<?php

namespace App\Imports;

use App\Accounts\Models\Account;
use App\Models\Tenant\Ledger;
use App\Models\Tenant\Sim;
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

class SimsImport implements ToCollection, WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows
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

        # Perform basic header validation on 1st row
        $errorFound = $this->headersValidation([
            'number',
            'company',
            'purchasingdate',
            'type',
        ], $rows->first()->toArray());

        if(!$errorFound){

            $this->_IH_init("sims");
            $this->_IH_addData([
                'total_records' => $rows->count()
            ]);

            $errors = [];

            foreach ($rows as $rowIndex => $row) {

                $rowNo = $rowIndex + 2; # +1 beacause index start at zero, +1 because we have header row

                $number = (string) trim($row['number']);
                $company = strtolower(trim($row['company']));
                $type = strtolower(trim($row['type']));
                $date= isset($row['purchasingdate']) && trim($row['purchasingdate']) !== '' ? Carbon::createFromFormat("d/m/Y", $row['purchasingdate'])->format('Y-m-d') : null;

                // Make sure number starts with '0'
                if(isset($number[0]) && $number[0] != "0"){
                    $number = "0".$number;
                }

                // Check if number exists in DB
                $exists = Sim::where('number', $number)->exists();
                if($exists){
                    $errors[] = "Row #$rowNo - Sim $number already exists";
                    continue;
                }

                $sim = new Sim;
                $sim->number = $number;
                $sim->company = $company;
                $sim->type = $type;
                $sim->purchasing_date = $date;
                $sim->save();

                $this->_IH_addRecord(get_class($sim), $sim->id);
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
            'number' => 'required|max:255',
            'company' => 'required|max:255',
            'purchasingdate' => 'nullable|date_format:d/m/Y',
            'type' => 'required|in:prepaid,postpaid',
        ];
    }

}
