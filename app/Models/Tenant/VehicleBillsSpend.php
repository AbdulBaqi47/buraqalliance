<?php

namespace App\Models\Tenant;

use App\Helpers\NullRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use App\Traits\AutoIncreamentTrait;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleBillsSpend extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    /**
     * Get the addon_setting that owns the Addon
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
