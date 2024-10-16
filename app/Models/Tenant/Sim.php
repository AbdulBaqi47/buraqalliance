<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Sim extends Model
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
        return $this->hasMany(SimEntity::class, 'sim_id');
    }

}
