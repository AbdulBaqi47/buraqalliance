<?php

namespace App\Models\Tenant;

use App\Traits\LogActivityTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class DriverBookingHistory extends Model
{
    use HasFactory, LogActivityTrait, SoftDeletes;
    protected $appends = ['days_assigned','status'];

    public function getDaysAssignedAttribute()
    {
        if(!$this->unassign_date || !isset($this->unassign_date)) return 0;
        $start_date = Carbon::parse($this->assign_date);
        $end_date = Carbon::parse($this->unassign_date);
        $daysBetween = $end_date->diffInDays($start_date);
        return $daysBetween;
    }
    public function getStatusAttribute()
    {
        if(isset($this->unassign_date) && $this->unassign_date !== ''){
            return 'inactive';
        }
        return 'active';
    }

    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function vehicle_booking(){
        return $this->belongsTo(VehicleBooking::class, 'booking_id');
    }
}
