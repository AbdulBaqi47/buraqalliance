<?php

namespace App\Exports;

use App\Invoice;
use App\Models\Tenant\Report;
use App\Models\Tenant\VehicleBooking;
use App\Models\Tenant\VehicleLedger;
use App\Models\Tenant\VehicleLedgerItem;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

# Events
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;


class ReportExport implements FromCollection, WithHeadings, WithEvents, ShouldQueue, ShouldAutoSize, WithStyles
{

    use Exportable;

    private $payload;
    private Report $report;
    private $filePath;
    private $log_channel = 'reports-cron';

    public function __construct($payload, $filePath)
    {
        $this->payload = $payload;
        $this->filePath = $filePath;

        $this->report = new Report;

        $this->report->type = $this->payload->type;
        $this->report->range = [
            'start' => $this->payload->start,
            'end' => $this->payload->end,
            'type' => $this->payload->range,
        ];
        $this->report->date = Carbon::now()->toAtomString();
        $this->report->status = "inprogress";
        $this->report->progress = 0;
        $this->report->attachment = null;
        $this->report->save();
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }


    public function collection()
    {

        switch ($this->payload->type) {
            case 'pending_balance':
                return $this->generate_pendingbalance();
                break;
            case 'income':
                return $this->generate_income();
                break;
            case 'salik':
                return $this->generate_salik();
                break;

            default:
                # code...
                break;
        }


        return collect([]);

    }


    public function headings(): array
    {
        switch ($this->payload->type) {
            case 'pending_balance':
                return [
                    "ID",
                    "Investor",
                    "Balance",
                ];
                break;
            case 'income':
                return [
                    "Booking / Vehicle",
                    "Date",
                    "Month",
                    "Title",
                    "Description",
                    "Driver",
                    "Amount",
                    "Attachment",
                ];
                break;
            case 'salik':
                return [
                    "Date",
                    "Amount",
                ];
                break;

            default:
                break;
        }


        return [
            "Date",
            "Title",
        ];
    }

    private function generate_pendingbalance() : Collection
    {
        $bookings = VehicleBooking::select('id', 'investor_id', 'status')
        ->with([
            'investor' => function($query){
                $query->select('id', 'name');
            },

            'vehicle' => function($query){
                $query->select('id', 'vehicle_booking_id', 'plate');
            },

        ])
        ->get();
        $bookingIds = $bookings->pluck('id')->toArray();

        $start = $this->payload->start;
        $end = $this->payload->end;


        $balance=0;
        # --------------------------------
        #       MONGODB aggregate
        # --------------------------------
        $vehicleLedgers = VehicleLedger::whereIn('vehicle_booking_id', $bookingIds)->select('_id', 'vehicle_booking_id')->get();
        $vehicleLedgerIds = $vehicleLedgers->pluck('_id')->unique()->values()->toArray();


        $aggs = VehicleLedgerItem::raw(function($collection) use ($vehicleLedgerIds, $start, $end){
            return $collection->aggregate([
                [
                    '$match'=> [
                        '$expr'=>[
                            '$and' => [
                                // [ '$gte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$start"] ] ],
                                [ '$lte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$end"] ] ]
                            ]
                        ],
                        "statement_ledger_id"=>['$in' => $vehicleLedgerIds],
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$group'=> [
                        "_id"=> '$statement_ledger_id',
                        "cr"=> [
                            '$sum'=> [
                                '$cond'=> [
                                    [ '$eq'=> [ '$type', 'dr' ] ],
                                    0,
                                    '$amount'

                                ]
                            ]
                        ],
                        "dr"=> [
                            '$sum'=> [
                                '$cond'=> [
                                    [ '$eq'=> [ '$type', 'cr' ] ],
                                    0,
                                    '$amount'

                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$project'=> [
                        '_id'=>1,
                        "cr"=> '$cr',
                        "dr"=> '$dr',
                        "balance" => [ '$subtract' => [ '$cr', '$dr' ] ],
                    ]
                ]
            ]);
        });


        return $bookings->map(function($booking) use ($aggs, $vehicleLedgers){

            $ledgerIds = $vehicleLedgers->where('vehicle_booking_id', $booking->id)->pluck('_id')->unique()->values()->toArray();
            $booking->balance = $aggs->whereIn('_id', $ledgerIds)->sum('balance');

            $id = '';

            if($booking->status === "closed"){

                $id = 'V#'.$booking->id.' / '.$booking->vehicle->plate;
            }
            else{
                $id = 'B#'.$booking->id;

            }

            return [
                $id, // ID
                $booking->investor->name, // Investor
                $booking->balance // Balance
            ];
        });


    }

    private function generate_income() : Collection
    {

        $start = $this->payload->start;
        $end = $this->payload->end;
        $range = $this->payload->range;


        $vehicle_ledger_items = VehicleLedgerItem::with([
            'vehicle_ledger.booking',
            'driver' => function($query){
                $query->select('id', 'name');
            }
        ]);

        if($range === "month"){
            // Match month
            $vehicle_ledger_items->whereRaw([
                '$expr'=>[
                    '$and' => [
                        [ '$eq'=> [ ['$toDate'=> '$month'], ['$toDate'=> "$start"] ] ]
                    ]
                ],
            ]);
        }
        else{
            // Match dates
            $vehicle_ledger_items->whereRaw([
                '$expr'=>[
                    '$and' => [
                        [ '$gte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$start"] ] ],
                        [ '$lte'=> [ ['$toDate'=> '$date'], ['$toDate'=> "$end"] ] ]
                    ]
                ],
            ]);
        }


        $vehicle_ledger_items = $vehicle_ledger_items
        ->where('tag', 'regexp', '/^company_income/i') // tags started with company_income
        ->orderBy('created_at')
        ->get();

        # Transform to return required format
        return $vehicle_ledger_items

        ->map(function($item){

            // Booking title
            $booking = $item->vehicle_ledger->booking;
            $id = '';
            if($booking->status === "closed"){

                $id = 'V#'.$booking->id.' / '.$booking->vehicle->plate;
            }
            else{
                $id = 'B#'.$booking->id;

            }

            // Driver
            $driver = '';
            if(isset($item->driver)) $driver = $item->driver->full_name;

            // Attachment
            $attachment = '';
            if(isset($item->attachment) && $item->attachment !== ''){
                $attachment = Storage::url($item->attachment);
            }

            return [
                $id, // ID
                Carbon::parse($item->date)->format('d/M/Y'), // Date
                Carbon::parse($item->month)->format('M Y'), // Month
                $item->title, // Title
                $item->description, // Description
                $driver, // Driver
                $item->amount, // Amount
                $attachment, // Attachment
            ];
        });

    }

    private function generate_salik() : Collection
    {

    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeExport::class => function(BeforeExport $event) {
                Log::channel($this->log_channel)->info("------------------------ [Report Generation ( START )] ------------------------");
                Log::channel($this->log_channel)->info(print_r($this->payload, true));




            },

            AfterSheet::class => function(AfterSheet $event) {

                $this->report->attachment = Storage::url($this->filePath);
                $this->report->status = "completed";
                $this->report->progress = 100;
                $this->report->update();

                Log::channel($this->log_channel)->info("------------------------ [Report Generation ( END )] ------------------------");
            },
        ];
    }
}
