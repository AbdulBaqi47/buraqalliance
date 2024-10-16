<?php

namespace App\Models\Tenant;

use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class VehicleEntity extends Model
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

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get the entities's source.
     */
    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
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

    /**
     * create/append the status attribute
     *
     *
    */
    public function getSourceNameAttribute()
    {

        $model = new $this->source_model;

        return $model->getTable();
    }
}
