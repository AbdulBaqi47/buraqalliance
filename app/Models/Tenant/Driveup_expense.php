<?php

namespace App\Models\Tenant;

use App\Traits\AutoIncreamentTrait;
use App\Traits\LogActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driveup_expense extends Model
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
