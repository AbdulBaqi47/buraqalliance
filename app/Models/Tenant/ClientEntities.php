<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class ClientEntities extends Model
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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
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

}
