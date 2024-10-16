<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogActivityTrait;

use App\Accounts\Handlers\AccountGateway;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use HasFactory,LogActivityTrait, SoftDeletes;


    protected $fillable = [
        'make', 'model', 'cc', 'notes'
    ];

    public function vehicles()
    {
        return $this->hasMany('App\Models\Tenant\Vehicle');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Tenant\VehicleBooking');
    }
}
