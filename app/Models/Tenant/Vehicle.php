<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    protected $appends = array('plate_title');

    public function vehicle_ledgers()
    {
        return $this->hasMany('App\Models\Tenant\VehicleLedger', 'vehicle_id');
    }

    public function vehicle_client_entities()
    {
        return $this->hasMany(ClientEntities::class, 'source_id')->where('source_model', Vehicle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * Get all of the addons for the Driver
     */
    public function addons()
    {
        return $this->hasMany(Addon::class, 'source_id')->where('source_type', 'vehicle');
    }
    public function entities()
    {
        return $this->hasMany(VehicleEntity::class, 'vehicle_id');
    }
    public function getPlateTitleAttribute()
    {
        if($this->plate_code == null){
            return  $this->plate;
        }
        return  $this->plate. '-' . $this->plate_code;
    }
     /**
     * Scope a query to only include perticular type of Vehicles (Bike or Vehicle).
     */
    public function scopeIs($query, $namespace): void
    {
        $query->where('type', $namespace === 'vehicle' ? 'vehicle' : 'bike');
    }
}
