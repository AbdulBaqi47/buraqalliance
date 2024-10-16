<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Driveup_ledger extends Model
{
    use HasFactory, LogActivityTrait, AutoIncreamentTrait;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function driver()
    {
        return $this->hasMany(Driver::class, 'driver_id');
    }
}