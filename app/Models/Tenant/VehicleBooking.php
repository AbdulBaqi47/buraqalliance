<?php

namespace App\Models\Tenant;

use App\Accounts\Traits\AccountRelationTrait;
use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleBooking extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, AccountRelationTrait, SoftDeletes;


    protected $fillable = [
        'investor_id', 'vehicle_type_id', 'vehicle_id', 'initial_amount', 'date', 'notes', 'status'
    ];

    public function vehicle_type()
    {
        return $this->belongsTo('App\Models\Tenant\VehicleType', 'vehicle_type_id');
    }

    public function vehicle()
    {
        return $this->hasOne('App\Models\Tenant\Vehicle', 'vehicle_booking_id');
    }

    public function reserve_vehicle()
    {
        return $this->hasOne('App\Models\Tenant\Vehicle', 'reserve_vehicle_booking_id');
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class, 'booking_id');
    }

    public function driveup_drivers()
    {
        return $this->hasMany(Driveup_booking_driver::class, 'booking_id');
    }

    public function investor()
    {
        return $this->belongsTo('App\Models\Tenant\Investor', 'investor_id');
    }

    public function vehicle_ledgers()
    {
        return $this->hasMany('App\Models\Tenant\VehicleLedger', 'vehicle_booking_id');
    }

    /**
     * Get all of the booking_history for the Driver
     */
    public function booking_history()
    {
        return $this->hasMany(DriverBookingHistory::class, 'booking_id');
    }

    /**
     * Get all of the vehicle_history for the Vehicle
     */
    public function vehicle_history()
    {
        return $this->hasMany(VehicleHistory::class, 'booking_id');
    }

    public function getClosingBalanceAttribute(){

        $bookingId = $this->id;
        $bookingBalances = self::fetchClosingBalance([$bookingId]);

        return isset($bookingBalances[$bookingId]) ? $bookingBalances[$bookingId] : 0;
    }


    /**
     * Get all of the sim_entities for the Booking
     */
    public function sim_entities()
    {
        return $this->hasMany(SimEntity::class, 'source_id')->where('source_model', VehicleBooking::class);
    }


    /**
     * -------------------
     *   HELPER METHODS
     * -------------------
    */

    /**
     * Get closing balance of bookings statements
     *
     * @param array $bookingIds VehicleBooking IDs
     * @param array $filter_dates Filter dates, if month is found, it will use 'month', if start/end found, it uses 'date'. format: ['start' => "2023-12-01", 'end' => "2023-12-31", 'month' => "2023-12-01"]
     * @param array $fiter_dates_range works only when $filter_dates has "month" field  with_previous => balance includes current month + previous all months | current_only => only mentioned month
     * @return array
     *
     */
    public static function fetchClosingBalance($bookingIds, $filter_dates = [], $fiter_dates_range = 'with_previous') : array
    {
        # --------------------------------------------
        #             MONGODB aggregate
        #  To fetch closing balance against bookings
        # --------------------------------------------

        # Fetch Vehicle Ledgers against booking
        # Later we will fetch statement of each bookings

        $vehicleLedgers = VehicleLedger::whereIn('vehicle_booking_id', $bookingIds)->select('_id', 'vehicle_booking_id')->get();
        $vehicleLedgerIds = $vehicleLedgers->pluck('_id')->unique()->values()->toArray();

        $agg = [
            [
                '$match'=> [

                    # If "month" found, it will match month field
                    # Else it will match dates with "start" & "end"
                    ...isset($filter_dates['month']) ? [
                        '$expr'=>[
                            '$and' => [
                                [ $fiter_dates_range === 'with_previous' ? '$lte' : '$eq' => [ ['$toDate'=> '$month'], ['$toDate'=> $filter_dates['month']] ] ]
                            ]
                        ]
                    ] : [...isset($filter_dates['start']) && isset($filter_dates['end']) ?  [
                        '$expr'=>[
                            '$and' => [
                                [ '$gte'=> [ ['$toDate'=> '$date'], ['$toDate'=> $filter_dates['start']] ] ],
                                [ '$lte'=> [ ['$toDate'=> '$date'], ['$toDate'=> $filter_dates['end']] ] ]
                            ]
                        ]
                    ] : []],


                    # Match booking ledger IDs
                    'statement_ledger_id' => ['$in' => $vehicleLedgerIds],

                    "deleted_at" => null // Exclude soft deleted
                ]
            ],
            [
                '$group'=> [
                    "_id"=> '$statement_ledger_id',
                    "balance"=> [
                        '$sum'=> [
                            '$cond'=> [
                                [ '$eq'=> [ '$type', 'dr' ] ],
                                [ '$subtract' => [ 0, ['$toDouble' => '$amount'] ] ],
                                ['$toDouble' => '$amount']

                            ]
                        ]
                     ]
                ]
            ]
        ];

        $aggData = VehicleLedgerItem::raw(function($collection) use ($vehicleLedgerIds, $filter_dates, $agg){
            return $collection->aggregate($agg);
        });

        # Map booking id
        return collect($bookingIds)->mapWithKeys(function($bookingId) use ($aggData, $vehicleLedgers){

            $ledgerIds = $vehicleLedgers->where('vehicle_booking_id', $bookingId)->pluck('_id')->unique()->values()->toArray();
            $balance = $aggData->whereIn('_id', $ledgerIds)->sum('balance');

            return [
                $bookingId => round($balance, 2)
            ];
        })
        ->toArray();

    }

}
