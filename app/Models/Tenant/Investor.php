<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogActivityTrait;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Investor extends Authenticatable
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    protected $guard = "investor";

    protected $fillable = [
        'id', 'email', 'name', 'phone', 'refid', 'images', 'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function vehicles()
    {
        return $this->hasMany('App\Models\Tenant\Vehicle', 'investor_id');
    }

    public function bookings()
    {
        return $this->hasMany(VehicleBooking::class, 'investor_id');
    }

    public function manages()
    {
        return $this->belongsToMany(Investor::class, null, 'manages_by', 'manages_to');
    }


    /**
     * create/append the balance attribute
     *
     *
    */
    public function getBalanceAttribute()
    {
        # calculate balance

        $this->loadMissing([
            'bookings' => function($query){
                $query->select('id', 'investor_id');
            }
        ]);

        $balance=0;
        # --------------------------------
        #       MONGODB aggregate
        # --------------------------------
        $bookingIds = $this->bookings->pluck('id')->unique()->values()->toArray();
        $vehicleLedgerIds = VehicleLedger::whereIn('vehicle_booking_id', $bookingIds)->select('_id', 'vehicle_booking_id')->pluck('_id')->unique()->values()->toArray();
        $agg = VehicleLedgerItem::raw(function($collection) use ($vehicleLedgerIds){
            return $collection->aggregate([
                [
                    '$match'=> [
                        "statement_ledger_id"=> ['$in' => $vehicleLedgerIds],
                        "deleted_at" => null // Exclude soft deleted
                    ]
                ],
                [
                    '$group'=> [
                        "_id"=> null,
                        "balance"=> [
                            '$sum'=> [
                                '$cond'=> [
                                    [ '$eq'=> [ '$type', 'dr' ] ],
                                    [ '$subtract' => [ 0, '$amount' ] ],
                                    '$amount'

                                ]
                            ]
                         ]
                    ]
                ]
            ]);
        })->first();

        if(isset($agg))$balance=$agg->balance;

        return $balance;
    }

}
