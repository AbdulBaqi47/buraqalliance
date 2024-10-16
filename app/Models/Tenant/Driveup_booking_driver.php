<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Driveup_booking_driver extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;

    protected $appends=['status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'assign_date' => 'date',
        'unassign_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(VehicleBooking::class, 'booking_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * create/append the status attribute
     *
     *
    */
    public function getStatusAttribute()
    {
        if(isset($this->unassign_date) && $this->unassign_date!==''){
            return 'inactive';
        }

        return 'active';
    }

}
