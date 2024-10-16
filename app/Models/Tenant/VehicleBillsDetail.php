<?php

namespace App\Models\Tenant;

use App\Accounts\Traits\TableRelationTrait;
use App\Helpers\NullRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogActivityTrait;
use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleBillsDetail extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes, TableRelationTrait;

    /**
     * Get the addon_setting that owns the Addon
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
