<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogActivityTrait;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function entities()
    {
        return $this->hasMany(ClientEntities::class, 'client_id');
    }


    /**
     * Scope a query to only include perticular type of client (aggregator or supplier).
     */
    public function scopeIs($query, $namespace): void
    {
        $query->where('source', $namespace === 'aggregator' ? 'driver' : 'vehicle');
    }

}
